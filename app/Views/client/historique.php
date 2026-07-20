<?= $this->extend('client/client_layout') ?>

<?= $this->section('title') ?>Mon Historique<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Historique des transactions</h2>
                <p class="text-secondary mb-0 small">Retrouvez l'intégralité de vos mouvements financiers MadaCash.</p>
            </div>
            <span class="badge bg-light text-dark border p-2 rounded-3">
                <i class="bi bi-phone me-1"></i> <?= esc($telephone) ?>
            </span>
        </div>

        <?php if (empty($historique)): ?>
            <!-- État vide -->
            <div class="text-center py-5">
                <i class="bi bi-arrow-left-right text-muted display-4 mb-3 d-block"></i>
                <p class="text-secondary fw-semibold">Aucune transaction enregistrée pour le moment.</p>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                    Faire une opération
                </a>
            </div>
        <?php else: ?>
            <!-- Tableau des transactions -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th>Référence</th>
                            <th>Date & Heure</th>
                            <th>Type</th>
                            <th>Mouvement</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historique as $row): ?>
                            <tr>
                                <td class="font-monospace fw-bold small text-secondary">
                                    <?= esc($row['reference'] ?? 'N/A') ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d/m/Y H:i', strtotime($row['date_mouvement'])) ?>
                                </td>
                                <td>
                                    <?php 
                                    $type = $row['type_operation'] ?? '';
                                    if ($type === 'DEP') echo '<span class="badge bg-info-subtle text-info rounded-pill px-2">Dépôt</span>';
                                    elseif ($type === 'RET') echo '<span class="badge bg-warning-subtle text-warning rounded-pill px-2">Retrait</span>';
                                    else echo '<span class="badge bg-primary-subtle text-primary rounded-pill px-2">Transfert</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if (($row['sens'] ?? '') === 'CREDIT'): ?>
                                        <span class="text-success small fw-bold"><i class="bi bi-arrow-down-left-circle-fill me-1"></i> Reçu</span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-bold"><i class="bi bi-arrow-up-right-circle-fill me-1"></i> Envoyé</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold <?= ($row['sens'] ?? '') === 'CREDIT' ? 'text-success' : 'text-dark' ?>">
                                    <?= ($row['sens'] ?? '') === 'CREDIT' ? '+' : '-' ?>&nbsp;<?= number_format($row['montant'], 2, ',', ' ') ?> AR
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
    </div>
</div>
<?= $this->endSection() ?>