<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Title Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.3/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Data Uji</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Komentar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $a = 1;
                        foreach ($csvData as $comment) { ?>
                            <tr>
                                <td><?= $a++ ?></td>
                                <td><?= $comment[3] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h4>Data Setelah Pre Processing</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Komentar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $a = 1;
                        for ($i = 0; $i < count($commentsArray); $i++) { ?>
                            <tr>
                                <td><?= $a++ ?></td>
                                <td><?= $commentsArray[$i] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h4>Probabilitas Kelas</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Probabilitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($class_prob as $label => $prob) : ?>
                            <tr>
                                <td><?= $label; ?></td>
                                <td><?= $prob; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h3>Probabilitas Kata dalam Kelas</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kata</th>
                            <?php foreach ($class_prob as $label => $prob) : ?>
                                <th><?= $label; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($word_prob as $word => $probs) : ?>
                            <tr>
                                <td><?= $word; ?></td>
                                <?php foreach ($probs as $label => $prob) : ?>
                                    <td><?= $prob; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h4>Data Prediksi</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Komentar</th>
                            <th>Prediksi</th>
                            <th>Angka Prediksi</th>
                            <th>Skor Prediksi</th>
                            <th>Score Tertinggi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($csvData as $index => $row) : ?>
                            <?php if ($index > 0) : ?>
                                <tr>
                                    <td><?= $row[3]; ?></td>
                                    <td><?= $predictions[$index - 1]['label']; ?></td>
                                    <td><?= $predictions[$index - 1]['label'] === 'positif' ? '1' : ($predictions[$index - 1]['label'] === 'negatif' ? '-1' : '0'); ?></td>
                                    <td><?= $predictions[$index - 1]['score']; ?></td>
                                    <td><?= $predictions[$index - 1]['highest_score']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <strong>Semua Skor Prediksi:</strong>
                                        <ul>
                                            <?php foreach ($predictions[$index - 1]['scores'] as $label => $score) : ?>
                                                <li><?= $label ?>: <?= $score ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h4>Hasil Prediksi</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Komentar</th>
                            <th>Prediksi</th>
                            <th>Angka Prediksi</th>
                            <th>Skor Prediksi</th>
                            <th>Score Tertinggi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($csvData as $index => $row) : ?>
                            <?php if ($index > 0) : ?>
                                <tr>
                                    <td><?= $row[3]; ?></td>
                                    <td><?= $predictions[$index - 1]['label']; ?></td>
                                    <td><?= $predictions[$index - 1]['label'] === 'positif' ? '1' : ($predictions[$index - 1]['label'] === 'negatif' ? '-1' : '0'); ?></td>
                                    <td><?= $predictions[$index - 1]['score']; ?></td>
                                    <td><?= $predictions[$index - 1]['highest_score']; ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <canvas id="predictionChart"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Data prediksi
                    var predictions = '<?= json_encode($predictions2); ?>';

                    // Menghitung jumlah prediksi positif, negatif, dan netral
                    var positiveCount = predictions.filter(prediction => prediction.label === 'positif').length;
                    var negativeCount = predictions.filter(prediction => prediction.label === 'negatif').length;
                    var neutralCount = predictions.filter(prediction => prediction.label === 'netral').length;

                    // Mengambil label kategori prediksi
                    var labels = ['Positif', 'Negatif', 'Netral'];

                    // Mengambil data jumlah prediksi
                    var data = [positiveCount, negativeCount, neutralCount];

                    // Membuat chart menggunakan Chart.js
                    var ctx = document.getElementById('predictionChart').getContext('2d');
                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Hasil Prediksi',
                                data: data,
                                backgroundColor: ['green', 'red', 'yellow'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    precision: 0,
                                    stepSize: 1
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>



    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>

</html>