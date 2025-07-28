<?php
wp_enqueue_script('pcb-admin', PCB_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PCB_VERSION, true);
wp_enqueue_style('pcb-admin', PCB_PLUGIN_URL . 'assets/css/admin.css', array(), PCB_VERSION);
wp_localize_script('pcb-admin', 'pcb_admin_ajax', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('pcb_admin_nonce')
));
?>
<?php
/**
 * Template for admin appointments list
 */
if (!current_user_can('manage_options')) {
    wp_die('Không có quyền truy cập.');
}

get_header();
?>

<div class="pcb-admin-appointments">
    <div class="pcb-admin-header">
        <h1>申し込み一覧</h1>
        <div class="pcb-admin-actions">
            <a href="#" class="pcb-btn pcb-btn-secondary" id="download-csv">この一覧をCSVでダウンロード</a>
        </div>
    </div>

    <!-- Search/Filter Section -->
    <div class="pcb-search-section">
        <form id="pcb-search-form">
            <div class="pcb-search-grid">
                <div class="pcb-search-item">
                    <label for="hospital_name">病院名</label>
                    <input type="text" id="hospital_name" name="hospital_name" placeholder="紹介病院名で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="owner_name">飼主名</label>
                    <input type="text" id="owner_name" name="owner_name" placeholder="飼主名で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="doctor_name">院長名/担当医</label>
                    <input type="text" id="doctor_name" name="doctor_name" placeholder="院長名/担当医で検索">
                </div>
                <div class="pcb-search-item">
                    <label for="department">診療科</label>
                    <input type="text" id="department" name="department" placeholder="診療科で検索">
                </div>
                <div class="pcb-search-item">
                    <button type="submit" class="pcb-btn pcb-btn-primary">検索</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="pcb-table-container">
        <table class="pcb-appointments-table">
            <thead>
                <tr>
                    <th>詳細</th>
                    <th>受付日時</th>
                    <th>予約希望日時</th>
                    <th>紹介病院名</th>
                    <th>飼主名</th>
                    <th>動物名/種</th>
                    <th>診療科</th>
                    <th>電話番号</th>
                    <th>ステータス</th>
                </tr>
            </thead>
            <tbody id="pcb-appointments-tbody">
                <!-- Appointments will be loaded here -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pcb-pagination" id="pcb-pagination">
        <!-- Pagination will be generated here -->
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPage = 1;
    let totalPages = 1;
    let searchParams = {};

    // Load appointments on page load
    loadAppointments();

    // Handle search form submission
    $('#pcb-search-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        searchParams = {
            hospital_name: $('#hospital_name').val(),
            owner_name: $('#owner_name').val(),
            doctor_name: $('#doctor_name').val(),
            department: $('#department').val()
        };
        loadAppointments();
    });

    // Handle CSV download
    $('#download-csv').on('click', function(e) {
        e.preventDefault();
        downloadCSV();
    });

    function loadAppointments() {
        $.ajax({
            url: pcb_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcb_admin_get_appointments',
                page: currentPage,
                search: searchParams,
                nonce: pcb_admin_ajax.nonce
            },
            beforeSend: function() {
                $('#pcb-appointments-tbody').html('<tr><td colspan="9" class="pcb-loading">読み込み中...</td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    displayAppointments(response.data.appointments);
                    displayPagination(response.data.pagination);
                } else {
                    $('#pcb-appointments-tbody').html('<tr><td colspan="9" class="pcb-error">' + response.data + '</td></tr>');
                }
            },
            error: function() {
                $('#pcb-appointments-tbody').html('<tr><td colspan="9" class="pcb-error">エラーが発生しました。</td></tr>');
            }
        });
    }

    function displayAppointments(appointments) {
        let html = '';

        if (appointments.length === 0) {
            html = '<tr><td colspan="9" class="pcb-empty">データが見つかりません。</td></tr>';
        } else {
            appointments.forEach(function(appointment) {
                let statusClass = getStatusClass(appointment.status);
                let statusText = getStatusText(appointment.status);

                html += '<tr>';
                html += '<td><a href="#" class="pcb-detail-link" data-id="' + appointment.id + '">表示</a></td>';
                html += '<td>' + formatDateTime(appointment.created_at) + '</td>';
                html += '<td>' + formatDateTime(appointment.preferred_date_1 + ' ' + appointment.preferred_time_1) + '</td>';
                html += '<td>' + (appointment.referral_hospital || '-') + '</td>';
                html += '<td>' + appointment.customer_name + '</td>';
                html += '<td>' + appointment.pet_name + '/' + appointment.pet_type + '</td>';
                html += '<td>' + appointment.department + '</td>';
                html += '<td>' + appointment.customer_phone + '</td>';
                html += '<td><span class="pcb-status ' + statusClass + '">' + statusText + '</span></td>';
                html += '</tr>';
            });
        }

        $('#pcb-appointments-tbody').html(html);
    }

    function displayPagination(pagination) {
        let html = '';

        if (pagination.total_pages > 1) {
            for (let i = 1; i <= pagination.total_pages; i++) {
                let pageClass = i === currentPage ? 'pcb-page-active' : 'pcb-page-inactive';
                html += '<button class="pcb-page-btn ' + pageClass + '" data-page="' + i + '">' + i + '</button>';
            }
        }

        $('#pcb-pagination').html(html);
    }

    function getStatusClass(status) {
        switch(status) {
            case 'pending':
                return 'pcb-status-pending';
            case 'confirmed':
                return 'pcb-status-confirmed';
            case 'completed':
                return 'pcb-status-completed';
            case 'cancelled':
                return 'pcb-status-cancelled';
            default:
                return 'pcb-status-pending';
        }
    }

    function getStatusText(status) {
        switch(status) {
            case 'pending':
                return '受付中';
            case 'confirmed':
                return '確認済み';
            case 'completed':
                return '完了';
            case 'cancelled':
                return 'キャンセル';
            default:
                return '受付中';
        }
    }

    function formatDateTime(dateTimeString) {
        if (!dateTimeString) return '-';
        let date = new Date(dateTimeString);
        return date.toLocaleString('ja-JP', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function downloadCSV() {
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "詳細,受付日時,予約希望日時,紹介病院名,飼主名,動物名/種,診療科,電話番号,ステータス\n";

        // Get current table data
        $('#pcb-appointments-tbody tr').each(function() {
            let row = [];
            $(this).find('td').each(function(index) {
                if (index === 0) {
                    row.push('表示'); // Detail link
                } else if (index === 8) {
                    row.push($(this).find('.pcb-status').text()); // Status text only
                } else {
                    row.push($(this).text().trim());
                }
            });
            csvContent += row.join(',') + '\n';
        });

        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "appointments_" + new Date().toISOString().slice(0,10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Handle pagination clicks
    $(document).on('click', '.pcb-page-btn', function() {
        currentPage = parseInt($(this).data('page'));
        loadAppointments();
    });

    // Handle detail link clicks
    $(document).on('click', '.pcb-detail-link', function(e) {
        e.preventDefault();
        let appointmentId = $(this).data('id');
        window.open('<?php echo home_url("/admin/appointment-detail/"); ?>' + appointmentId + '/', '_blank');
    });
});
</script>

<?php get_footer(); ?>
