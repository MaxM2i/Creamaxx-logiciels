# Créamaxx — site vitrine

Site responsive en HTML, CSS et JavaScript, avec traitement PHP du formulaire de contact et envoi des pièces jointes par e-mail.

## Prévisualiser

Ouvrir `index.html` dans un navigateur pour consulter les pages statiques. Les polices Google nécessitent une connexion internet ; des polices système prennent le relais hors ligne.

## Activer réellement le formulaire

Le formulaire de contact transmet les champs et pièces jointes à `contact.php`. Il faut un hébergement compatible PHP (PHP 8 recommandé), avec `fileinfo` et `mbstring`, et une fonction `mail()` configurée par l'hébergeur.

Avant publication :

1. Dans `contact.php`, remplacer `$recipient = 'contact@VOTRE-DOMAINE.fr'` par l'adresse qui doit recevoir les demandes.
2. Remplacer `$fromDomain = 'VOTRE-DOMAINE.fr'` par le domaine réellement utilisé pour le site. Configurer SPF, DKIM et DMARC chez le fournisseur DNS / e-mail pour améliorer la délivrabilité.
3. Vérifier que l'hébergeur autorise les e-mails sortants et que PHP accepte les fichiers de taille suffisante : `upload_max_filesize` au moins 15M et `post_max_size` au moins 80M (ou davantage selon les réglages retenus).
4. Tester l'envoi avec plusieurs formats, un fichier trop volumineux, un format refusé et une adresse invalide.
5. Mettre le site en HTTPS. Ne pas utiliser le formulaire en production avant configuration et tests.

Le formulaire accepte jusqu'à 5 pièces jointes de 15 Mo chacune : PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, ODT, ODS, TXT, ZIP, PNG et JPG/JPEG. Les fichiers sont transmis en pièces jointes e-mail et ne sont pas archivés dans le dossier public du site. La limite réelle d'envoi dépend aussi de l'hébergeur et de la taille maximale des e-mails.

## Personnalisation obligatoire avant mise en ligne

- Remplacer les textes provisoires de `apropos.html` par le nom, la photo, le parcours et les compétences réelles du développeur.
- Renseigner les coordonnées, l'identité de l'éditeur et l'hébergeur dans `mentions-legales.html`.
- Compléter `confidentialite.html` avec les durées de conservation, les destinataires, les sous-traitants, les droits et le contact responsable du traitement. Ces pages sont des modèles, pas un avis juridique.
- Vérifier que les études de cas reflètent bien l'état réel des projets. Les deux exemples sont explicitement présentés comme des cahiers des charges / conceptions, pas comme des logiciels déployés.
- Vérifier la disponibilité du nom Créamaxx et du nom de domaine avant usage commercial.

## Arborescence

- `index.html` : accueil
- `solutions.html` : offres
- `realisations.html` : portfolio / études de cas
- `methode.html` : méthode de travail
- `apropos.html` : présentation
- `contact.html` : formulaire avec ajout de cahier des charges et pièces jointes
- `contact.php` : réception et envoi e-mail (à configurer)
- `mentions-legales.html`, `confidentialite.html` : modèles à compléter
- `assets/` : styles et scripts
