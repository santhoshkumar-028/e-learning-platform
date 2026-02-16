<?php
// delete.php
session_start();
require 'db_connect.php';

// require admin
if (!isset($_SESSION['admin_id'])) {
    // AJAX or normal
    if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
        exit;
    } else {
        header('Location: admin_login.php');
        exit;
    }
}

$isAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';
$action = $_POST['action'] ?? '';
$result = ['ok' => false];

if ($action === 'delete_course') {
    $courseId = (int)($_POST['course_id'] ?? 0);
    if ($courseId <= 0) {
        $result['error'] = 'Invalid course id.';
    } else {
        // get image/video
        $stmt = $conn->prepare("SELECT image, video FROM courses WHERE id = ? LIMIT 1");
        $img = $vid = '';
        if ($stmt) {
            $stmt->bind_param('i', $courseId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && ($row = $res->fetch_assoc())) {
                $img = $row['image'] ?? '';
                $vid = $row['video'] ?? '';
            }
            $stmt->close();
        }

        $conn->begin_transaction();
        try {
            // delete quizzes related
            $q = $conn->prepare("DELETE FROM quizzes WHERE course_id = ?");
            if ($q) { $q->bind_param('i', $courseId); $q->execute(); $q->close(); }

            // delete course row
            $d = $conn->prepare("DELETE FROM courses WHERE id = ?");
            if (!$d) throw new Exception('DB prepare failed: ' . $conn->error);
            $d->bind_param('i', $courseId);
            if (!$d->execute()) {
                $err = $d->error;
                $d->close();
                throw new Exception('DB error while deleting course: ' . $err);
            }
            $d->close();

            $conn->commit();

            // remove files after commit
            if (!empty($img)) {
                $p = __DIR__ . '/' . $img;
                if (file_exists($p)) @unlink($p);
            }
            if (!empty($vid)) {
                $p2 = __DIR__ . '/' . $vid;
                if (file_exists($p2)) @unlink($p2);
            }

            $result['ok'] = true;
            $result['msg'] = 'Course deleted.';
        } catch (Exception $e) {
            $conn->rollback();
            $result['error'] = $e->getMessage();
        }
    }

} elseif ($action === 'delete_user') {
    $userId = (int)($_POST['user_id'] ?? 0);
    if ($userId <= 0) {
        $result['error'] = 'Invalid user id.';
    } else {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            if ($stmt->execute()) {
                $stmt->close();
                $result['ok'] = true;
                $result['msg'] = 'User deleted.';
            } else {
                $result['error'] = 'DB error: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $result['error'] = 'DB prepare error: ' . $conn->error;
        }
    }
} else {
    $result['error'] = 'Unknown action.';
}

// respond
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
    exit;
} else {
    // redirect back with message
    $msg = $result['ok'] ? ($result['msg'] ?? 'Deleted') : ($result['error'] ?? 'Error');
    header('Location: admin_dashboard.php?msg=' . urlencode($msg));
    exit;
}
