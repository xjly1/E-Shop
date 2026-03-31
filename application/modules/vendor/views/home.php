<?php if ($this->session->flashdata('update_vend_err')) { ?>
    <div class="alert alert-danger"><?= implode('<br>', $this->session->flashdata('update_vend_err')) ?></div>
<?php } ?>

<?php if ($this->session->flashdata('update_vend_details')) { ?>
    <div class="alert alert-success"><?= $this->session->flashdata('update_vend_details') ?></div>
<?php } ?>

<div class="content vendor-dashboard-card">
    <div class="vendor-card-head">
        <h3>Monthly Orders</h3>
        <p>Orders overview by month</p>
    </div>

    <script src="<?= base_url('assets/highcharts/highcharts.js') ?>"></script>
    <div id="container-by-month" style="min-width: 310px; height: 400px; margin: 0 auto;"></div>

    <script>
        $(function () {
            Highcharts.chart('container-by-month', {
                chart: {
                    backgroundColor: '#ffffff',
                    borderRadius: 12
                },
                title: {
                    text: 'Monthly Orders',
                    style: {
                        fontSize: '22px',
                        fontWeight: '600'
                    }
                },
                subtitle: {
                    text: 'Source: Orders table'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yAxis: {
                    title: {
                        text: 'Orders'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#d1d5db'
                    }]
                },
                tooltip: {
                    valueSuffix: ' Orders'
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                },
                series: [
<?php foreach ($ordersByMonth['years'] as $year) { ?>
                    {
                        name: '<?= $year ?>',
                        data: [<?= implode(',', $ordersByMonth['orders'][$year]) ?>]
                    },
<?php } ?>
                ]
            });
        });
    </script>
</div>