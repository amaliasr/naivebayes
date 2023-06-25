<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Welcome extends CI_Controller
{
	public function index()
	{
		$this->load->view('index');
	}
	public function importCsv()
	{
		$this->load->model('naive_bayes_model');

		// DATA UJI
		// Mengambil path file CSV yang diupload
		$filePath = $_FILES['csv_file']['tmp_name'];

		// Menggunakan PhpSpreadsheet untuk membaca file CSV
		$spreadsheet = IOFactory::load($filePath);
		$worksheet = $spreadsheet->getActiveSheet();

		// Mendapatkan data dari file CSV
		$csvData = [];
		foreach ($worksheet->getRowIterator() as $row) {
			$rowData = [];
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE);
			foreach ($cellIterator as $cell) {
				$rowData[] = $cell->getValue();
			}
			$csvData[] = $rowData;
		}

		// Proses data CSV sesuai kebutuhan Anda
		// Misalnya, simpan data ke database atau tampilkan di halaman
		// sastrawi
		$stopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
		$stopword = $stopWordRemoverFactory->createStopWordRemover();
		$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
		$stemmer = $stemmerFactory->createStemmer();
		// Contoh: Menampilkan data CSV ke halaman
		$commentsArray = [];
		$a = 0;
		foreach ($csvData as $comment) {
			if ($a > 0) {
				$text = $comment[3];
				$commentsArray[] = $stemmer->stem($stopword->remove($this->naive_bayes_model->preprocess($text)));
				// $commentsArray[] = $this->naive_bayes_model->preprocess($text);
			}
			$a++;
		}

		// Contoh data latih
		$X_train = array(
			"sangat bagus",
			"terlalu jelek",
			"sangat baik",
			"sangat buruk",
			"luar biasa",
			"kurang memuaskan",
			"sangat cantik",
			"sangat indah",
			"warna tidak menarik",
			"tidak menarik",
			"harga murah",
			"harga berapa",
			"ukuran berapa",
			"pakai warna apa ya",
		);

		$y_train = array(
			"positif",
			"negatif",
			"positif",
			"negatif",
			"positif",
			"negatif",
			"positif",
			"positif",
			"negatif",
			"negatif",
			"positif",
			"netral",
			"netral",
			"netral",
		);

		// Contoh data uji
		$X_test = $commentsArray;

		// Melatih model Naive Bayes
		$training_result = $this->naive_bayes_model->train($X_train, $y_train);
		$class_prob = $training_result['class_prob'];
		$word_prob = $training_result['word_prob'];

		// Melakukan prediksi
		$predictions = $this->naive_bayes_model->predict($X_test);
		$predictionData = array();
		foreach ($predictions as $prediction) {
			$predictionData[] = array('label' => $prediction);
		}

		// Menampilkan hasil prediksi
		$data['csvData'] = $csvData;
		$data['commentsArray'] = $commentsArray;
		$data['predictions'] = $predictions;
		$data['predictions2'] = $predictionData;
		$data['class_prob'] = $class_prob;
		$data['word_prob'] = $word_prob;
		$this->load->view('hasil', $data);
	}
}
