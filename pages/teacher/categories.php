<?php
/**
 * Page: Gestion des Catégories
 * Permet de créer, modifier et supprimer des catégories
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'categories';
$pageTitle = 'Catégories';

// Récupérer les données
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$categoryObj = new Category();
$categories = $categoryObj->getAllByTeacher($teacherId);

// Messages
$success = $_SESSION['category_success'] ?? '';
$error = $_SESSION['category_error'] ?? '';
unset($_SESSION['category_success'], $_SESSION['category_error']);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Gestion des Catégories</h2>
                <p class="text-gray-600 mt-2">Organisez vos quiz par catégories</p>
            </div>
            <button onclick="openModal()"
                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Nouvelle Catégorie
            </button>
        </div>

        <?php include '../partials/alerts.php'; ?>

        <!-- Categories List -->
        <?php if (empty($categories)): ?>
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune catégorie</h3>
                <p class="text-gray-600 mb-6">Créez votre première catégorie pour organiser vos quiz</p>
                <button onclick="openModal()"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i>Créer une Catégorie
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $colors = ['blue', 'purple', 'green', 'red', 'yellow', 'pink', 'indigo', 'teal'];
                foreach ($categories as $index => $category):
                    $color = $colors[$index % count($colors)];
                    ?>
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-<?= $color ?>-500">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($category['nom']) ?></h3>
                                <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($category['description']) ?></p>
                            </div>
                            <div class="flex gap-2">
                                <a href="category_edit.php?id=<?= $category['id'] ?>" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../../actions/teacher/category_delete.php?id=<?= $category['id'] ?>&token=<?= Security::generateCSRFToken() ?>"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
                                    class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i><?= $category['quiz_count'] ?>
                                quiz</span>
                            <span class="text-gray-500 text-xs"><?= date('d/m/Y', strtotime($category['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Créer Catégorie -->
<div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Nouvelle Catégorie</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="../../actions/teacher/category_create.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Nom de la catégorie *
                    </label>
                    <input type="text" name="nom" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Ex: HTML/CSS">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Décrivez cette catégorie..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-check mr-2"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    window.onclick = function (event) {
        const modal = document.getElementById('createModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php include '../partials/footer.php'; ?>