<?php
/**
 * Page: Modifier un Quiz
 * Permet de modifier un quiz et ses questions
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'quiz';
$pageTitle = 'Modifier Quiz';

// Récupérer les données
$quizId = intval($_GET['id'] ?? 0);
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$quizObj = new Quiz();
$quiz = $quizObj->getById($quizId);

// Vérifier que le quiz existe et appartient à l'enseignant
if (!$quiz || !$quizObj->isOwner($quizId, $teacherId)) {
    $_SESSION['quiz_error'] = 'Quiz non trouvé ou accès refusé';
    header('Location: quiz.php');
    exit();
}

$questionObj = new Question();
$questions = $questionObj->getAllByQuiz($quizId);

$categoryObj = new Category();
$categories = $categoryObj->getAllByTeacher($teacherId);

// Messages
$error = $_SESSION['quiz_error'] ?? '';
$success = $_SESSION['quiz_success'] ?? '';
unset($_SESSION['quiz_error'], $_SESSION['quiz_success']);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<div class="pt-16">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Header -->
                <div class="mb-8">
                    <a href="quiz.php" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux quiz
                    </a>
                    <h2 class="text-3xl font-bold text-gray-900">Modifier le Quiz</h2>
                </div>

                <?php include '../partials/alerts.php'; ?>

                <!-- Formulaire de modification du quiz -->
                <form action="../../actions/teacher/quiz_update.php" method="POST" class="mb-8">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Informations du Quiz</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Titre *</label>
                            <input type="text" name="titre" required value="<?= htmlspecialchars($quiz['titre']) ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catégorie *</label>
                            <select name="categorie_id" required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $quiz['categorie_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars($quiz['description']) ?></textarea>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                        </button>
                        
                        <!-- Toggle Actif/Inactif -->
                        <a href="../../actions/teacher/quiz_toggle.php?id=<?= $quiz['id'] ?>&token=<?= Security::generateCSRFToken() ?>" 
                           class="px-6 py-3 rounded-lg font-semibold transition <?= $quiz['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                            <i class="fas fa-power-off mr-2"></i>
                            <?= $quiz['is_active'] ? 'Actif' : 'Inactif' ?>
                        </a>
                    </div>
                </form>

                <hr class="my-8">

                <!-- Gestion des Questions -->
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Questions (<?= count($questions) ?>)</h3>
                        <button onclick="openAddQuestionModal()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-2"></i>Ajouter une question
                        </button>
                    </div>

                    <?php if (empty($questions)): ?>
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <i class="fas fa-question-circle text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-600">Aucune question. Ajoutez-en au moins une.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-bold text-gray-900">Question <?= $index + 1 ?></h4>
                                        <div class="flex gap-2">
                                            <button onclick="editQuestion(<?= $question['id'] ?>)" 
                                                    class="text-blue-600 hover:text-blue-700">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="../../actions/teacher/question_delete.php?id=<?= $question['id'] ?>&quiz_id=<?= $quizId ?>&token=<?= Security::generateCSRFToken() ?>"
                                               onclick="return confirm('Supprimer cette question ?')"
                                               class="text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="text-gray-900 mb-3 font-medium"><?= htmlspecialchars($question['question']) ?></p>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <div class="flex items-center <?= $question['correct_option'] == $i ? 'text-green-600 font-bold' : 'text-gray-600' ?>">
                                                <?php if ($question['correct_option'] == $i): ?>
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                <?php else: ?>
                                                    <i class="far fa-circle mr-2"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($question['option' . $i]) ?>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Question -->
<div id="addQuestionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 my-8">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Ajouter une Question</h3>
                <button onclick="closeAddQuestionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="../../actions/teacher/question_create.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                    <input type="text" name="question" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 1 *</label>
                        <input type="text" name="option1" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 2 *</label>
                        <input type="text" name="option2" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 3 *</label>
                        <input type="text" name="option3" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 4 *</label>
                        <input type="text" name="option4" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                    <select name="correct_option" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Sélectionner</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeAddQuestionModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Question -->
<div id="editQuestionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 my-8">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-edit mr-2 text-blue-600"></i>Modifier la Question
                </h3>
                <button onclick="closeEditQuestionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="../../actions/teacher/question_update.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                <input type="hidden" name="question_id" id="edit_question_id" value="">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                    <input type="text" name="question" id="edit_question" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 1 *</label>
                        <input type="text" name="option1" id="edit_option1" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 2 *</label>
                        <input type="text" name="option2" id="edit_option2" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 3 *</label>
                        <input type="text" name="option3" id="edit_option3" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 4 *</label>
                        <input type="text" name="option4" id="edit_option4" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                    <select name="correct_option" id="edit_correct_option" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditQuestionModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Données des questions pour JavaScript
    var questionsData = <?= json_encode($questions) ?>;
    
    // Modal Ajouter
    function openAddQuestionModal() {
        document.getElementById('addQuestionModal').classList.remove('hidden');
    }
    
    function closeAddQuestionModal() {
        document.getElementById('addQuestionModal').classList.add('hidden');
    }
    
    // Modal Modifier
    function openEditQuestionModal() {
        document.getElementById('editQuestionModal').classList.remove('hidden');
    }
    
    function closeEditQuestionModal() {
        document.getElementById('editQuestionModal').classList.add('hidden');
    }
    
    // Fonction pour éditer une question
    function editQuestion(questionId) {
        var question = questionsData.find(function(q) {
            return q.id == questionId;
        });
        
        if (question) {
            document.getElementById('edit_question_id').value = question.id;
            document.getElementById('edit_question').value = question.question;
            document.getElementById('edit_option1').value = question.option1;
            document.getElementById('edit_option2').value = question.option2;
            document.getElementById('edit_option3').value = question.option3;
            document.getElementById('edit_option4').value = question.option4;
            document.getElementById('edit_correct_option').value = question.correct_option;
            
            openEditQuestionModal();
        }
    }
</script>

<?php include '../partials/footer.php'; ?>

