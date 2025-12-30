<?php
/**
 * Page: Modifier une Catégorie
 * Formulaire de modification de catégorie
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'categories';
$pageTitle = 'Modifier Catégorie';

// Récupérer les données
$categoryId = intval($_GET['id'] ?? 0);
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$categoryObj = new Category();
$category = $categoryObj->getById($categoryId);

// Vérifier que la catégorie existe et appartient à l'enseignant
if (!$category || $category['created_by'] != $teacherId) {
    $_SESSION['category_error'] = 'Catégorie non trouvée ou accès refusé';
    header('Location: categories.php');
    exit();
}

// Messages
$error = $_SESSION['category_error'] ?? '';
$success = $_SESSION['category_success'] ?? '';
unset($_SESSION['category_error'], $_SESSION['category_success']);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<div class="pt-16">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Header -->
                <div class="mb-8">
                    <a href="categories.php" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux catégories
                    </a>
                    <h2 class="text-3xl font-bold text-gray-900">Modifier la Catégorie</h2>
                </div>

                <?php include '../partials/alerts.php'; ?>

                <!-- Formulaire -->
                <form action="../../actions/teacher/category_update.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-folder mr-2"></i>Nom de la catégorie *
                        </label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($category['nom']) ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-align-left mr-2"></i>Description
                        </label>
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>

                    <div class="flex gap-4">
                        <a href="categories.php"
                            class="flex-1 text-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Annuler
                        </a>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>