<?php

require_once 'models/functions.php';

$autofillUser = null;
if (isset($_SESSION['user']['id'])) {
    $stmtUser = getDB()->prepare("SELECT * FROM users WHERE id = ?");
    $stmtUser->execute([$_SESSION['user']['id']]);
    $autofillUser = $stmtUser->fetch();
}

$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $format = $_POST['format_type'] ?? 'A5';
    $cover = $_POST['cover_type'] ?? 'soft';
    $paper = $_POST['paper_type'] ?? 'classic';
    $pages = (int)($_POST['pages'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1);
    
    $formatLabels = ['A5'=>'A5 (Standard)', 'B6'=>'B6 (Tankobon)', 'A4'=>'Grand Format (A4)'];
    $coverLabels = ['soft'=>'Souple Mate', 'soft_glossy'=>'Souple Brillante', 'hard'=>'Édition Rigide'];
    $paperLabels = ['classic'=>'Bouffant 90g (Crème Manga)', 'standard'=>'Offset 80g (Blanc standard)', 'luxe'=>'Couché Mat 115g (Luxe Illustration)'];
    
    $friendlyFormat = $formatLabels[$format] ?? $format;
    $friendlyCover = $coverLabels[$cover] ?? $cover;
    $friendlyPaper = $paperLabels[$paper] ?? $paper;

    
    $fileInfo = '';
    if (isset($_FILES['manga_file']) && $_FILES['manga_file']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['manga_file'];
        $uploadDir = 'uploads/prints/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $cleanFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($uploadedFile['name']));
        $targetPath = $uploadDir . $cleanFileName;
        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $fileInfo = "\nFichier joint : " . $uploadedFile['name'] . " (sauvegardé sous : " . $targetPath . ")";
        }
    }

    $rawMessage = trim($_POST['message'] ?? '');
    
    
    $composedMessage = "Spécifications du projet :\n"
                     . "- Format : $friendlyFormat\n"
                     . "- Couverture : $friendlyCover\n"
                     . "- Papier : $friendlyPaper\n"
                     . "- Pages : $pages pages\n"
                     . "- Quantité : $qty exemplaires\n"
                     . $fileInfo . "\n\n"
                     . "Message additionnel du client :\n"
                     . ($rawMessage ?: "Aucun commentaire.");

    $data = [
        'name'    => trim($_POST['name'] ?? ''),
        'email'   => trim($_POST['email'] ?? ''),
        'format'  => $format,
        'pages'   => $pages,
        'qty'     => $qty,
        'cover'   => $cover,
        'message' => $composedMessage
    ];

    if (!$data['name'] || !$data['email'] || $data['pages'] <= 0) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        if (createDevis($data)) {
            $success = true;
        } else {
            $error = 'Une erreur est survenue lors de l\'envoi de votre demande.';
        }
    }
}

$pageTitle = 'Impression sur demande';
require_once 'views/print.php';
