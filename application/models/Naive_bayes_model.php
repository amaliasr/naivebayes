<?php
class Naive_bayes_model extends CI_Model
{
    private $vocab = array();
    private $class_prob = array();
    private $word_prob = array();

    public function train($X_train, $y_train)
    {
        $this->class_prob = $this->calculateClassProb($y_train);
        $this->word_prob = $this->calculateWordProb($X_train, $y_train);
    }

    private function calculateClassProb($y_train)
    {
        $class_counts = array();
        $total_samples = count($y_train);

        foreach ($y_train as $label) {
            $class_counts[$label] = isset($class_counts[$label]) ? $class_counts[$label] + 1 : 1;
        }

        $class_prob = array();
        foreach ($class_counts as $label => $count) {
            $class_prob[$label] = $count / $total_samples;
        }

        return $class_prob;
    }

    public function calculateWordProb($X_train, $y_train)
    {
        $word_counts = array();
        $class_word_counts = array();
        $tfidf = $this->calculateTFIDF($X_train);

        for ($i = 0; $i < count($X_train); $i++) {
            $X = $X_train[$i];
            $y = $y_train[$i];

            if (!isset($class_word_counts[$y])) {
                $class_word_counts[$y] = array();
            }

            $words = explode(' ', $X);
            foreach ($words as $word) {
                $this->vocab[] = $word;

                if (!isset($word_counts[$word])) {
                    $word_counts[$word] = array();
                }

                if (!isset($word_counts[$word][$y])) {
                    $word_counts[$word][$y] = 0;
                }

                if (!isset($class_word_counts[$y][$word])) {
                    $class_word_counts[$y][$word] = 0;
                }

                $word_counts[$word][$y]++;
                $class_word_counts[$y][$word]++;
            }
        }

        $word_prob = array();
        foreach ($this->vocab as $word) {
            $word_prob[$word] = array();

            foreach ($class_word_counts as $label => $class_counts) {
                if (isset($class_counts[$word])) {
                    if (isset($tfidf[$word])) {
                        $nilaiTFIDF = $tfidf[$word];
                    } else {
                        $nilaiTFIDF = 1;
                    }
                    $word_prob[$word][$label] = $class_counts[$word] / array_sum($class_counts) * $nilaiTFIDF;
                } else {
                    $word_prob[$word][$label] = 0;
                }
            }
        }

        return $word_prob;
    }



    public function calculateTFIDF($X_train)
    {
        $word_counts = array();
        $doc_counts = array();
        foreach ($X_train as $X) {
            $words = explode(' ', $X);
            $words = array_unique($words);

            foreach ($words as $word) {
                if (!isset($word_counts[$word])) {
                    $word_counts[$word] = 0;
                }

                $word_counts[$word]++;
            }

            foreach ($words as $word) {
                if (!isset($doc_counts[$word])) {
                    $doc_counts[$word] = 0;
                }

                $doc_counts[$word]++;
            }
        }

        $tfidf = array();
        foreach ($this->vocab as $word) {
            $tf = isset($word_counts[$word]) ? $word_counts[$word] : 0;
            $idf = log(count($X_train) / ($doc_counts[$word] + 1));
            $tfidf[$word] = $tf * $idf;
        }

        return $tfidf;
    }

    public function predict($X_test)
    {
        $predictions = array();

        foreach ($X_test as $X) {
            $class_scores = array();

            foreach ($this->class_prob as $label => $prob) {
                $class_scores[$label] = log($prob);

                $words = explode(' ', $X);
                foreach ($words as $word) {
                    if (in_array($word, $this->vocab)) {
                        $class_scores[$label] += log($this->word_prob[$word][$label]);
                    }
                }
            }

            $predicted_label = array_search(max($class_scores), $class_scores);
            $predictions[] = $predicted_label;
        }

        return $predictions;
    }
    public function preprocess($text)
    {
        // Case Folding
        $text = strtolower($text);
        // Tokenizing
        $words = explode(' ', $text);

        // Stopword Removal
        $stopwords = $this->getStopwords(); // Mendapatkan daftar stopwords
        $words = array_diff($words, $stopwords);

        // Stemming
        $words = $this->stem($words);

        // Mengembalikan teks yang sudah dipreprocessing
        $text = implode(' ', $words);

        return $text;
    }

    private function getStopwords()
    {
        // Daftar stopwords yang ingin dihilangkan
        $stopwords = array('yang', 'dan', 'ke', 'dari', 'mana', 'itu', 'ini');
        return $stopwords;
    }

    private function stem($words)
    {
        // Implementasi algoritma stemming yang sesuai dengan bahasa yang digunakan
        // Misalnya, menggunakan algoritma Porter untuk Bahasa Inggris
        // Anda dapat menggunakan library seperti "PorterStemmer" untuk melakukan stemming

        // Contoh implementasi sederhana tanpa algoritma sebenarnya
        $stemmed_words = array();
        foreach ($words as $word) {
            $stemmed_word = $this->customStemming($word); // Fungsi custom untuk stemming
            $stemmed_words[] = $stemmed_word;
        }

        return $stemmed_words;
    }

    private function customStemming($word)
    {
        // Implementasi algoritma stemming khusus sesuai dengan kebutuhan Anda
        // Misalnya, menggunakan aturan-aturan tertentu untuk memotong akhiran kata

        // Contoh implementasi sederhana tanpa algoritma sebenarnya
        if (substr($word, -3) == 'ing') {
            $word = substr($word, 0, -3);
        }

        return $word;
    }
}
