<?php
session_start();
require 'db_connect.php'; // must set $conn (mysqli)

// require admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$msg = '';

// --- Handle add course ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_course') {
    $title = trim($_POST['title'] ?? '');
    $short = trim($_POST['short_desc'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imagePath = '';
    $videoPath = '';

    // image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        $targetDir = __DIR__ . '/uploads/images/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $filename = basename($_FILES['image']['name']);
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $target = $targetDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // store relative path for web usage
            $imagePath = 'uploads/images/' . $filename;
        }
    }

    // video upload
    if (!empty($_FILES['video']['tmp_name'])) {
        $targetDirV = __DIR__ . '/uploads/videos/';
        if (!is_dir($targetDirV)) mkdir($targetDirV, 0755, true);
        $vname = basename($_FILES['video']['name']);
        $vname = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $vname);
        $targetv = $targetDirV . $vname;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $targetv)) {
            $videoPath = 'uploads/videos/' . $vname;
        }
    }

   // DOCUMENT upload (NEW)
    if (!empty($_FILES['document']['tmp_name'])) {
        $targetDirD = __DIR__ . '/uploads/documents/';
        if (!is_dir($targetDirD)) mkdir($targetDirD, 0755, true);
        $dname = basename($_FILES['document']['name']);
        $dname = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $dname);
        $targetd = $targetDirD . $dname;
        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetd)) {
            $documentPath = 'uploads/documents/' . $dname;
        }
    }

    // UPDATED INSERT (document added)
    $stmt = $conn->prepare("INSERT INTO courses (title, short_desc, content, image, video, document) VALUES (?,?,?,?,?,?)");
    if ($stmt) {
        $stmt->bind_param('ssssss', $title, $short, $content, $imagePath, $videoPath, $documentPath);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: admin_dashboard.php?msg=' . urlencode('Course added'));
            exit;
        } else {
            $msg = 'DB error adding course: ' . $stmt->error;
            $stmt->close();
        }
    } else {
        $msg = 'DB prepare error: ' . $conn->error;
    }
}

// --- Handle add quiz (single or bulk) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_quiz') {
    $course_id = (int)($_POST['course_id'] ?? 0);
    $bulk = trim($_POST['bulk_quiz'] ?? '');

    if ($bulk !== '') {
        // bulk parse
        $lines = preg_split("/\r\n|\n|\r/", $bulk);
        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = array_map('trim', explode('||', $line));
            if (count($parts) < 6) continue;
            $q = $parts[0]; $o1 = $parts[1]; $o2 = $parts[2]; $o3 = $parts[3]; $o4 = $parts[4];
            $k = (int)$parts[5];
            if ($k < 1 || $k > 4) $k = 1;
            $rows[] = [$course_id, $q, $o1, $o2, $o3, $o4, $k];
        }

        if (count($rows) < 15) {
            $msg = 'Bulk add requires at least 15 valid questions. Found: ' . count($rows);
        } else {
            $conn->begin_transaction();
            $ok = true;
            $stmt = $conn->prepare("INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES (?,?,?,?,?,?,?)");
            if (!$stmt) {
                $ok = false;
                $msg = 'Prepare failed: ' . $conn->error;
            } else {
                foreach ($rows as $r) {
                    [$cid,$q,$o1,$o2,$o3,$o4,$k] = $r;
                    $stmt->bind_param('isssssi', $cid, $q, $o1, $o2, $o3, $o4, $k);
                    if (!$stmt->execute()) {
                        $ok = false;
                        $msg = 'Insert failed: ' . $stmt->error;
                        break;
                    }
                }
                $stmt->close();
            }

            if ($ok) {
                $conn->commit();
                header('Location: admin_dashboard.php?msg=' . urlencode('Bulk questions added: ' . count($rows)));
                exit;
            } else {
                $conn->rollback();
            }
        }
    } else {
        // single
        $q = trim($_POST['question'] ?? '');
        $o1 = trim($_POST['opt1'] ?? ''); $o2 = trim($_POST['opt2'] ?? ''); $o3 = trim($_POST['opt3'] ?? ''); $o4 = trim($_POST['opt4'] ?? '');
        $k = (int)($_POST['answer_key'] ?? 0);
        $stmt = $conn->prepare("INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES (?,?,?,?,?,?,?)");
        if ($stmt) {
            $stmt->bind_param('isssssi', $course_id, $q, $o1, $o2, $o3, $o4, $k);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: admin_dashboard.php?msg=' . urlencode('Question added'));
                exit;
            } else {
                $msg = 'DB error while adding quiz: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $msg = 'DB prepare error: ' . $conn->error;
        }
    }
}

// Fetch courses and students for display
$coursesRes = $conn->query("SELECT * FROM courses ORDER BY id DESC");
$allc = $coursesRes ? $coursesRes->fetch_all(MYSQLI_ASSOC) : [];

$studentsRes = $conn->query("SELECT * FROM students ORDER BY id ASC");
$allStudents = $studentsRes ? $studentsRes->fetch_all(MYSQLI_ASSOC) : [];

// detect if students table has phone column
$hasPhone = false;
$colRes = $conn->query("SHOW COLUMNS FROM students LIKE 'phone'");
if ($colRes && $colRes->num_rows > 0) $hasPhone = true;

// optional msg from redirect
if (isset($_GET['msg']) && $_GET['msg'] !== '') $msg = $_GET['msg'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* keep styling minimal and similar */
    body {
  background: linear-gradient(-45deg, #667eea, #764ba2, #ff6a88, #ffcc70);
  background-size: 400% 400%;
  animation: gradientBG 12s ease infinite;
  min-height: 100vh;
}

/* Smooth animated gradient */
@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  25% { background-position: 50% 100%; }
  50% { background-position: 100% 50%; }
  75% { background-position: 50% 0%; }
  100% { background-position: 0% 50%; }
}

/* Sections little glass effect */
.section {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(6px);
}

    .admin-wrap { max-width:1200px;margin:16px auto;padding:0 16px; }
    .section { background:#fff;padding:18px;border-radius:10px;box-shadow:0 8px 20px rgba(11,15,30,0.04); margin-bottom:18px; }
    .flex-row { display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap; }
    .left-col { flex:1 1 420px; min-width:300px; }
    .right-col { flex:1 1 520px; min-width:300px; }
    table.admin-table { width:100%; border-collapse:collapse; margin-top:12px; }
    table.admin-table th, table.admin-table td { padding:12px 10px; border-bottom:1px solid #f0f0f0; text-align:left; font-size:14px; }
    table.admin-table thead th { background:#fafafa; font-weight:700; color:#333; }
    .action-btn { border:0; padding:8px 10px; border-radius:6px; cursor:pointer; font-weight:700; }
    .btn-success { background:#10b981;color:#fff; } .btn-danger { background:#ef4444;color:#fff; }
    .img-thumb { width:120px;height:68px;object-fit:cover;border-radius:6px;border:1px solid #eee; }
    .hint { color:#666; font-size:13px; margin-top:6px; }
    form.inline { display:inline-block; margin:0; }
  </style>
  <script>
    function confirmSubmit(form, text) {
      return confirm(text || 'Are you sure?');
    }
  </script>
</head>
<body>
  <header class="site-header"><div class="brand">Admin Panel</div><nav><a href="index.php">Home</a> | <a href="logout.php">Logout</a></nav></header>

  <div class="admin-wrap">
    <?php if ($msg): ?>
      <div style="background:#ecfdf5;border-left:4px solid #10b981;padding:12px 14px;border-radius:6px;margin-bottom:12px;color:#064e3b">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>

    <div class="section flex-row">
      <div class="left-col">
        <h3>Add Course (image & video optional)</h3>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_course" />
          <label class="small">Title</label>
          <input name="title" required />
          <label class="small">Short description</label>
          <input name="short_desc" />
          <label class="small">Content</label>
          <textarea name="content" rows="4"></textarea>

          <label class="small">Image (jpg/png)</label>
          <input type="file" name="image" accept="image/*" />

          <label class="small">Video (mp4)</label>
          <input type="file" name="video" accept="video/mp4,video/*" />
<label class="small">Notes / Documents (PDF, DOC)</label>
<input type="file" name="document" accept=".pdf,.doc,.docx" />

          <div class="controls">
            <button type="submit" class="action-btn btn-success">Add Course</button>
          </div>
        </form>
      </div>

      <div class="right-col">
        <h3>Add Quiz</h3>
        <form method="post" style="margin-bottom:14px">
          <input type="hidden" name="action" value="add_quiz" />
          <label class="small">Course</label>
          <select name="course_id" required>
            <?php foreach($allc as $c): ?>
              <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
            <?php endforeach; ?>
          </select>

          <label class="small">Question</label>
          <textarea name="question" rows="3"></textarea>

          <label class="small">Option 1</label><input name="opt1" />
          <label class="small">Option 2</label><input name="opt2" />
          <label class="small">Option 3</label><input name="opt3" />
          <label class="small">Option 4</label><input name="opt4" />
          <label class="small">Answer Key (1-4)</label><input name="answer_key" />

          <div class="controls">
            <button type="submit" class="action-btn btn-success">Add Single Question</button>
          </div>
        </form>

        <div class="bulk-box">
          <form method="post">
            <input type="hidden" name="action" value="add_quiz" />
            <label class="small">Course (bulk)</label>
            <select name="course_id" required>
              <?php foreach($allc as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
              <?php endforeach; ?>
            </select>

            <label class="small" style="margin-top:10px">Bulk questions (minimum 15). Use <strong>||</strong> to separate fields:</label>
            <textarea name="bulk_quiz" rows="12" placeholder="Question || Option1 || Option2 || Option3 || Option4 || AnswerKey (1-4)"></textarea>
            <div class="hint">Each non-empty line is parsed as one question. At least 15 valid lines required.</div>

            <div class="controls" style="margin-top:10px">
              <button type="submit" class="action-btn btn-success">Add Bulk Questions</button>
            </div>
          </form>
        </div>

      </div>
    </div>

    <!-- Courses list -->
    <div class="section">
      <h3 style="margin-top:0">Existing Courses</h3>
      <?php if (empty($allc)): ?>
        <p class="hint">No courses yet.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead>
            <tr><th>#</th><th>Image</th><th>Title</th><th>Short Description</th><th style="width:220px;text-align:center">Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach($allc as $idx => $c): ?>
              <tr>
                <td><?php echo $idx+1; ?></td>
                <td>
                  <?php if (!empty($c['image']) && file_exists($c['image'])): ?>
                    <img src="<?php echo htmlspecialchars($c['image']); ?>" class="img-thumb" alt="thumb"/>
                  <?php else: ?>
                    <div style="width:120px;height:68px;background:#fafafa;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#999;font-size:12px">No image</div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($c['title']); ?></td>
                <td><?php echo htmlspecialchars($c['short_desc']); ?></td>
                <td style="text-align:center">
                  <a class="action-btn btn-success" href="course.php?id=<?php echo (int)$c['id']; ?>">View</a>
<a class="action-btn" style="background:#3b82f6;color:#fff"
   href="edit_course.php?id=<?php echo (int)$c['id']; ?>">
   Edit
</a>

                  <form method="post" action="delete.php" class="inline" onsubmit="return confirmSubmit(this, 'Delete course <?php echo htmlspecialchars(addslashes($c['title'])); ?>? This will remove associated quizzes too.')">
                    <input type="hidden" name="action" value="delete_course">
                    <input type="hidden" name="course_id" value="<?php echo (int)$c['id']; ?>">
                    <button type="submit" class="action-btn btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- Users list -->
    <div class="section">
      <h3>User List</h3>
      <?php if (empty($allStudents)): ?>
        <p class="hint">No users found.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead>
            <tr><th>#</th><th>Name</th><th>Email</th><th><?php echo $hasPhone ? 'Phone Number' : 'Phone'; ?></th><th style="width:140px;text-align:center">Action</th></tr>
          </thead>
          <tbody>
            <?php foreach($allStudents as $i => $u): ?>
              <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo htmlspecialchars($u['name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                <td><?php echo $hasPhone ? htmlspecialchars($u['phone'] ?? '-') : '-'; ?></td>
                <td style="text-align:center">
                  <button class="action-btn btn-success" type="button" onclick="location.href='mailto:<?php echo htmlspecialchars($u['email'] ?? ''); ?>'">Email</button>

                  <form method="post" action="delete.php" class="inline" onsubmit="return confirmSubmit(this, 'Delete user <?php echo htmlspecialchars(addslashes($u['name'] ?? 'User')); ?>?')">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                    <button type="submit" class="action-btn btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
