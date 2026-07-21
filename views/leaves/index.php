<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Leave Applications</h2>
    <a href="index.php?route=leaves-create" class="btn btn-primary">Apply for Leave</a>
</div>

<form class="row g-3 mb-4" method="GET" action="index.php">
    <input type="hidden" name="route" value="leaves">
    <div class="col-md-4">
        <input type="text" name="employee_name" class="form-control" placeholder="Search by employee" value="<?= htmlspecialchars($_GET['employee_name'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= (isset($_GET['status']) && $_GET['status'] === 'Approved') ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= (isset($_GET['status']) && $_GET['status'] === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="index.php?route=leaves" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<table class="table table-striped bg-white shadow-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Employee Name</th>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($leaves as $leave): ?>
        <tr>
            <td><?= $leave['id'] ?></td>
            <td><strong><?= htmlspecialchars($leave['employee_name']) ?></strong></td>
            <td><?php
                $leaveTypeLabels = ['S' => 'Sick', 'C' => 'Casual', 'P' => 'Paid'];
                echo htmlspecialchars($leaveTypeLabels[$leave['leave_type']] ?? $leave['leave_type'] ?? '');
            ?></td>
            <td><?= $leave['start_date'] ?></td>
            <td><?= $leave['end_date'] ?></td>
            <td><?= htmlspecialchars($leave['reason']) ?></td>
            <td><?= htmlspecialchars($leave['status']) ?></td>
            <td>
                <?php if ($leave['status'] === 'Pending'): ?>
                    <a href="index.php?route=leaves-approve&id=<?= $leave['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                    <a href="index.php?route=leaves-reject&id=<?= $leave['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                <?php else: ?>
                    <span class="text-muted">No actions</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>