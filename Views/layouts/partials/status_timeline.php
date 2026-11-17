<?php
/**
 * Shared Status Timeline Component
 * Variables expected: $statusLogs (array), $currentStatus (string)
 */

$allStatuses = [
    'pending'    => ['label' => 'Menunggu Konfirmasi', 'icon' => 'bi-hourglass-split',     'color' => 'secondary'],
    'assigned'   => ['label' => 'Ditugaskan ke Teknisi', 'icon' => 'bi-person-check-fill',  'color' => 'primary'],
    'accepted'   => ['label' => 'Diterima Teknisi',     'icon' => 'bi-check-circle-fill',   'color' => 'info'],
    'on_the_way' => ['label' => 'Teknisi Dalam Perjalanan', 'icon' => 'bi-truck',            'color' => 'warning'],
    'repairing'  => ['label' => 'Sedang Diperbaiki',    'icon' => 'bi-tools',               'color' => 'warning'],
    'completed'  => ['label' => 'Selesai Dikerjakan',   'icon' => 'bi-check-all',           'color' => 'success'],
    'cancelled'  => ['label' => 'Dibatalkan',           'icon' => 'bi-x-circle-fill',       'color' => 'danger'],
];

// Build a map of status -> log entry for timestamps
$logsByStatus = [];
foreach ($statusLogs as $log) {
    $logsByStatus[$log['status']] = $log;
}

$isCancelled = ($currentStatus === 'cancelled');
$displayOrder = $isCancelled
    ? ['pending', 'cancelled']
    : ['pending', 'assigned', 'accepted', 'on_the_way', 'repairing', 'completed'];

// Determine which statuses are "done"
$doneStatuses = array_keys(array_filter($logsByStatus));
?>

<div class="status-timeline py-2">
    <?php foreach ($displayOrder as $index => $statusKey): ?>
        <?php
            $meta      = $allStatuses[$statusKey] ?? ['label' => $statusKey, 'icon' => 'bi-circle', 'color' => 'secondary'];
            $logEntry  = $logsByStatus[$statusKey] ?? null;
            $isDone    = isset($logsByStatus[$statusKey]);
            $isCurrent = ($statusKey === $currentStatus);
            $isLast    = ($index === count($displayOrder) - 1);
        ?>
        <div class="d-flex align-items-start gap-3 mb-0">

            <!-- Connector line + icon -->
            <div class="d-flex flex-column align-items-center" style="min-width: 32px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm
                    <?= $isDone ? "bg-{$meta['color']} text-white" : 'bg-light border text-muted' ?>"
                    style="width: 32px; height: 32px; font-size: 14px; flex-shrink: 0;">
                    <i class="<?= $meta['icon'] ?>"></i>
                </div>
                <?php if (!$isLast): ?>
                    <div class="<?= $isDone ? "bg-{$meta['color']}" : 'bg-light border-start' ?>"
                        style="width: 2px; height: 36px; margin-top: 2px; opacity: <?= $isDone ? '0.4' : '0.3' ?>;"></div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="pb-2 flex-grow-1" style="margin-top: 4px;">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-semibold <?= $isCurrent ? "text-{$meta['color']}" : ($isDone ? 'text-dark' : 'text-muted') ?> small">
                        <?= htmlspecialchars($meta['label']) ?>
                    </span>
                    <?php if ($isCurrent && !$isCancelled): ?>
                        <span class="badge bg-<?= $meta['color'] ?>-subtle text-<?= $meta['color'] ?> border border-<?= $meta['color'] ?>-subtle rounded-pill" style="font-size: 10px;">
                            ● Status Saat Ini
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($logEntry): ?>
                    <div class="small text-muted mt-1">
                        <i class="bi bi-clock me-1"></i>
                        <?= htmlspecialchars(date('d M Y, H:i', strtotime($logEntry['created_at']))) ?>
                    </div>
                    <?php if (!empty($logEntry['note'])): ?>
                        <div class="small text-secondary mt-1 fst-italic">
                            <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($logEntry['note']) ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="small text-muted mt-1 fst-italic">Belum tercapai</div>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>
</div>
