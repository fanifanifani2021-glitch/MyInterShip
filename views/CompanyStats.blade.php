<div class="modal fade" id="ModalStats" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header no-bd">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">
                        Company Stats for <?= $company_data['name'] ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form-row">
                <div class="form-group col-md-4">
                    <label>Total Internship</label>
                    <input type="text" class="form-control" value="<?= $company_stats['total_internship'] ?>" />
                </div>

                <div class="form-group col-md-4">
                    <label>Ongoing Internship</label>
                    <input type="text" class="form-control" value="<?= $company_stats['accepted_internship'] ?>" />
                </div>

                <div class="form-group col-md-4">
                    <label>Finished Internship</label>
                    <input type="text" class="form-control" value="<?= $company_stats['finished_internship'] ?>" />
                </div>

                <div class="form-group col-md-12">
                    <label>Currently Submitted (Waiting fo Approval)</label>
                    <input type="text" class="form-control" value="<?= $company_stats['waiting_internship'] ?>" />
                </div>

            </div>



            <div class="modal-body form-row">
                <div class="form-group col-md-12">
                    <label>Feedback from Alumni about this Company</label>

                </div>

                <div class="form-group col-md-12">


                    <canvas id="radarChart" width="400" height="400"></canvas>


                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Radar chart configuration
    var config = {
        type: 'radar',
        data: {
            labels: ["Posisi", "Ilmu dari Poltek", "Ilmu Baru", "Data Magang"],
            datasets: [{
                data: [<?= $averages['rata_posisi'] ?>, <?= $averages['rata_ilmu_dari_poltek'] ?>, <?= $averages['rata_ilmu_baru'] ?>, <?= $averages['rata_data_magang'] ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            scale: {
                ticks: {
                    beginAtZero: true,
                    max: 4, // Since values are between 1 to 4
                }
            }
        }
    };

    // Create the chart
    var ctx = document.getElementById('radarChart').getContext('2d');
    var myRadarChart = new Chart(ctx, config);
</script>