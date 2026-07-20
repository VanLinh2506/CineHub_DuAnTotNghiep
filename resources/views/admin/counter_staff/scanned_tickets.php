<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-ticket-alt"></i> Vé đã quét hôm nay</h5>
    
    <div class="mb-3">
        <form method="GET" action="/counter/scanned" class="d-flex gap-2">
            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>" style="max-width: 200px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Xem
            </button>
        </form>
    </div>
    
    <?php if ($tickets->isEmpty()): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có vé nào được quét trong ngày này.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Ngày chiếu</th>
                        <th>Phòng</th>
                        <th>Ghế</th>
                        <th>Thời gian quét</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket->bookingPending->qr_code ?? $ticket->bookingPending->id ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($ticket->user->name ?? 'Khách lẻ'); ?></td>
                            <td><?php echo htmlspecialchars($ticket->showtime->movie->title ?? 'N/A'); ?></td>
                            <td><?php echo $ticket->showtime && $ticket->showtime->show_date ? date('d/m/Y H:i', strtotime($ticket->showtime->show_date . ' ' . $ticket->showtime->show_time)) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($ticket->showtime->screen->screen_name ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($ticket->seat); ?></td>
                            <td><?php echo $ticket->picked_up_at ? date('d/m/Y H:i:s', strtotime($ticket->picked_up_at)) : 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($tickets->hasPages()): ?>
            <div class="d-flex justify-content-center">
                <?php echo $tickets->appends(['date' => $date])->links(); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
