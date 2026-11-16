<?php
// Configuration : remplacez cette adresse par votre adresse de réception.
$recipient = 'contact@VOTRE-DOMAINE.fr';
$fromDomain = 'VOTRE-DOMAINE.fr';
$maxFileSize = 15 * 1024 * 1024;
$maxFiles = 5;
$allowed = [
  'pdf' => 'application/pdf',
  'doc' => 'application/msword',
  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'xls' => 'application/vnd.ms-excel',
  'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'ppt' => 'application/vnd.ms-powerpoint',
  'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  'odt' => 'application/vnd.oasis.opendocument.text',
  'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
  'txt' => 'text/plain',
  'zip' => 'application/zip',
  'png' => 'image/png',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg'
];
function failRequest($message, $code = 400)
{
  http_response_code($code);
  echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Envoi impossible — Créamaxx</title><body style="font:16px Arial;max-width:650px;margin:12vh auto;padding:24px;color:#17283b"><h1>Votre demande n’a pas pu être envoyée</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><p><a href="contact.html">← Retour au formulaire</a></p></body></html>';
  exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: contact.html');
  exit;
}
if (!empty($_POST['website'])) {
  failRequest('Demande non valide.');
}
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL) || strpos($recipient, 'VOTRE-DOMAINE') !== false) {
  failRequest('Le formulaire doit être configuré par le propriétaire du site avant sa mise en service.', 503);
}
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$company = trim((string) ($_POST['company'] ?? ''));
$industry = trim((string) ($_POST['industry'] ?? ''));
$need = trim((string) ($_POST['need'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $need === '' || $message === '' || ($_POST['consent'] ?? '') !== 'yes') {
  failRequest('Merci de renseigner les champs obligatoires et de donner votre accord.');
}
if (mb_strlen($name) > 160 || mb_strlen($company) > 200 || mb_strlen($message) > 12000) {
  failRequest('Certains champs dépassent la longueur autorisée.');
}
$files = [];
if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
  $count = count(array_filter($_FILES['attachments']['name'], fn($n) => $n !== ''));
  if ($count > $maxFiles)
    failRequest('5 fichiers maximum sont acceptés.');
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
    if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_NO_FILE)
      continue;
    if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK)
      failRequest('Un fichier n’a pas pu être reçu. Réessayez ou envoyez votre demande sans cette pièce jointe.');
    $tmp = $_FILES['attachments']['tmp_name'][$i];
    $original = basename((string) $_FILES['attachments']['name'][$i]);
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $size = (int) $_FILES['attachments']['size'][$i];
    if (!isset($allowed[$ext]))
      failRequest('Format de fichier non autorisé : ' . $original);
    if ($size > $maxFileSize)
      failRequest('Chaque fichier doit faire 15 Mo maximum.');
    $mime = $finfo->file($tmp);
    // MIME autorisés explicitement ; certains formats Office peuvent être détectés comme application/zip.
    $mimeAliases = ['docx' => ['application/zip', 'application/octet-stream'], 'xlsx' => ['application/zip', 'application/octet-stream'], 'pptx' => ['application/zip', 'application/octet-stream'], 'odt' => ['application/zip', 'application/octet-stream'], 'ods' => ['application/zip', 'application/octet-stream'], 'zip' => ['application/x-zip'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg']];
    if ($mime !== $allowed[$ext] && !in_array($mime, $mimeAliases[$ext] ?? [], true))
      failRequest('Le type réel du fichier ne correspond pas à son extension : ' . $original);
    $files[] = ['name' => $original, 'path' => $tmp, 'mime' => $allowed[$ext]];
  }
}
$boundary = '=_Creamaxx_' . bin2hex(random_bytes(16));
$subject = '=?UTF-8?B?' . base64_encode('Nouvelle demande de projet — ' . $name) . '?=';
$body = "Nouvelle demande reçue depuis le site Créamaxx\n\nNom : $name\nE-mail : $email\nEntreprise : $company\nSecteur : $industry\nBesoin : $need\n\nMessage :\n$message\n\nNombre de pièces jointes : " . count($files) . "\n";
$body = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . $body . "\r\n";
foreach ($files as $file) {
  $content = chunk_split(base64_encode(file_get_contents($file['path'])));
  $safeName = addcslashes($file['name'], "\\\"");
  $body .= "--$boundary\r\nContent-Type: {$file['mime']}; name=\"$safeName\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$safeName\"\r\n\r\n$content\r\n";
}
$body .= "--$boundary--\r\n";
$host = preg_replace('/[^a-z0-9.-]/i', '', $fromDomain);
$headers = "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
$headers .= 'From: Créamaxx <no-reply@' . $host . '>' . "\r\n";
$headers .= 'Reply-To: ' . str_replace(["\r", "\n"], '', $email) . "\r\n";
if (!mail($recipient, $subject, $body, $headers))
  failRequest('Le serveur n’a pas pu transmettre le message. Contactez l’administrateur du site.', 500);
header('Location: contact.html?sent=1', true, 303);
exit;
