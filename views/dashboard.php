<?php

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-success active" href="#"><i class="bi bi-speedometer2"></i> <?= t('nav_dashboard') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#"><i class="bi bi-folder2-open"></i> <?= t('nav_cases') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#"><i class="bi bi-camera"></i> <?= t('nav_evidence') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#"><i class="bi bi-file-earmark-text"></i> <?= t('nav_reports') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#"><i class="bi bi-bar-chart"></i> Analytics</a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h1 class="text-success border-bottom border-success pb-2 mb-4">
                <i class="bi bi-speedometer2"></i> <?= t('dashboard') ?>
            </h1>

            <!-- Stats cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-folder-fill display-4 text-success"></i>
                            <h2 class="text-success">42</h2>
                            <p class="mb-0"><?= t('open_cases') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle-fill display-4 text-warning"></i>
                            <h2 class="text-warning">187</h2>
                            <p class="mb-0"><?= t('closed_cases') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill display-4 text-info"></i>
                            <h2 class="text-info">12</h2>
                            <p class="mb-0"><?= t('active_agents') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-danger">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle-fill display-4 text-danger"></i>
                            <h2 class="text-danger">HIGH</h2>
                            <p class="mb-0"><?= t('threat_level') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent activity table -->
            <h2 class="text-success"><i class="bi bi-clock-history"></i> <?= t('recent_activity') ?></h2>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover border-success">
                    <thead class="table-dark">
                        <tr class="text-success">
                            <th><?= t('case_id') ?></th>
                            <th><?= t('case_title') ?></th>
                            <th><?= t('case_agent') ?></th>
                            <th><?= t('case_status') ?></th>
                            <th><?= t('case_date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>XF-1013</td>
                            <td>Unexplained lights over Nevada</td>
                            <td>Fox Mulder</td>
                            <td><span class="badge bg-warning">Open</span></td>
                            <td>2026-03-01</td>
                        </tr>
                        <tr>
                            <td>XF-1012</td>
                            <td>Missing persons - Pine Barrens</td>
                            <td>Dana Scully</td>
                            <td><span class="badge bg-success">Closed</span></td>
                            <td>2026-02-28</td>
                        </tr>
                        <tr>
                            <td>XF-1011</td>
                            <td>Crop circle formation - Kansas</td>
                            <td>Fox Mulder</td>
                            <td><span class="badge bg-warning">Open</span></td>
                            <td>2026-02-25</td>
                        </tr>
                        <tr>
                            <td>XF-1010</td>
                            <td>Unidentified signal - SETI array</td>
                            <td>Walter Skinner</td>
                            <td><span class="badge bg-danger">Critical</span></td>
                            <td>2026-02-20</td>
                        </tr>
                        <tr>
                            <td>XF-1009</td>
                            <td>Government facility break-in</td>
                            <td>Dana Scully</td>
                            <td><span class="badge bg-success">Closed</span></td>
                            <td>2026-02-15</td>
                        </tr>
                        <tr>
                            <td>XF-1008</td>
                            <td>Paranormal activity - Oregon</td>
                            <td>Fox Mulder</td>
                            <td><span class="badge bg-warning">Open</span></td>
                            <td>2026-02-10</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
