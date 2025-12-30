<?php
/**
 * Page: Créer un Quiz
 * Formulaire de création de quiz
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'quiz';
$pageTitle = 'Créer un Quiz';

// Récupérer les catégories
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$categoryObj = new Category();
$categories = $categoryObj->getAllByTeacher($teacherId);

// Messages
$error = $_SESSION['quiz_error'] ?? '';
unset($_SESSION['quiz_error']);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<div class="pt-16">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Header -->
                <div class="mb-8">
                    <a href="quiz.php" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux quiz
                    </a>
                    <h2 class="text-3xl font-bold text-gray-900">Créer un nouveau Quiz</h2>
                    <p class="text-gray-600 mt-2">Remplissez les informations pour créer votre quiz</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($categories)): ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Vous devez d'abord créer une catégorie.
                        <a href="categories.php" class="underline font-semibold">Créer une catégorie</a>
                    </div>
                <?php else: ?>
                    <form action="../../actions/teacher/quiz_create.php" method="POST" id="quizForm">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-heading mr-2"></i>Titre du Quiz *
                            </label>
                            <input type="text" name="titre" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Ex: Quiz HTML/CSS Niveau 1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-folder mr-2"></i>Catégorie *
                            </label>
                            <select name="categorie_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Sélectionnez une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-align-left mr-2"></i>Description
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Décrivez votre quiz..."></textarea>
                        </div>

                        <hr class="my-6">

                        <!-- Section Questions -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-900">
                                    <i class="fas fa-question-circle mr-2 text-indigo-600"></i>Questions
                                </h3>
                                <button type="button" onclick="addQuestion()"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                    <i class="fas fa-plus mr-2"></i>Ajouter une question
                                </button>
                            </div>

                            <p class="text-gray-600 text-sm mb-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Vous devez ajouter au moins une question pour créer le quiz.
                            </p>

                            <div id="questionsContainer">
                                <!-- Les questions seront ajoutées ici dynamiquement -->
                            </div>

                            <div id="noQuestionsMsg" class="bg-gray-50 rounded-lg p-6 text-center">
                                <i class="fas fa-question-circle text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Aucune question ajoutée. Cliquez sur "Ajouter une question" pour
                                    commencer.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <a href="quiz.php"
                                class="flex-1 text-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Annuler
                            </a>
                            <button type="submit" id="submitBtn"
                                class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                                <i class="fas fa-plus-circle mr-2"></i>Créer le Quiz
                            </button>
                        </div>
                    </form>

                    <script>
                        let questionCount = 0;

                        function addQuestion() {
                            const container = document.getElementById('questionsContainer');
                            const noMsg = document.getElementById('noQuestionsMsg');
                            noMsg.style.display = 'none';

                            const questionIndex = questionCount;
                            questionCount++;

                            const questionDiv = document.createElement('div');
                            questionDiv.className = 'bg-gray-50 rounded-lg p-5 mb-4 question-block';
                            questionDiv.id = 'question_' + questionIndex;
                            questionDiv.innerHTML = `
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-bold text-gray-900">
                                        <i class="fas fa-question mr-2 text-indigo-600"></i>Question ${questionCount}
                                    </h4>
                                    <button type="button" onclick="removeQuestion(${questionIndex})" 
                                            class="text-red-600 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Supprimer
                                    </button>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Intitulé de la question *</label>
                                    <input type="text" name="questions[${questionIndex}][question]" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           placeholder="Entrez votre question...">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 1 *</label>
                                        <input type="text" name="questions[${questionIndex}][option1]" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Option 1">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 2 *</label>
                                        <input type="text" name="questions[${questionIndex}][option2]" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Option 2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 3 *</label>
                                        <input type="text" name="questions[${questionIndex}][option3]" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Option 3">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 4 *</label>
                                        <input type="text" name="questions[${questionIndex}][option4]" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Option 4">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Réponse correcte *</label>
                                    <select name="questions[${questionIndex}][correct_option]" required 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        <option value="">Sélectionnez la bonne réponse</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                        <option value="4">Option 4</option>
                                    </select>
                                </div>
                            `;

                            container.appendChild(questionDiv);
                            updateQuestionNumbers();
                        }

                        function removeQuestion(index) {
                            const questionDiv = document.getElementById('question_' + index);
                            if (questionDiv) {
                                questionDiv.remove();
                                updateQuestionNumbers();

                                // Vérifier s'il reste des questions
                                const container = document.getElementById('questionsContainer');
                                const noMsg = document.getElementById('noQuestionsMsg');
                                if (container.children.length === 0) {
                                    noMsg.style.display = 'block';
                                }
                            }
                        }

                        function updateQuestionNumbers() {
                            const questions = document.querySelectorAll('.question-block');
                            questions.forEach((q, idx) => {
                                const title = q.querySelector('h4');
                                if (title) {
                                    title.innerHTML = `<i class="fas fa-question mr-2 text-indigo-600"></i>Question ${idx + 1}`;
                                }
                            });
                        }

                        // Validation avant soumission
                        document.getElementById('quizForm').addEventListener('submit', function (e) {
                            const container = document.getElementById('questionsContainer');
                            if (container.children.length === 0) {
                                e.preventDefault();
                                alert('Vous devez ajouter au moins une question pour créer le quiz.');
                                return false;
                            }
                        });

                        // Ajouter une première question par défaut
                        document.addEventListener('DOMContentLoaded', function () {
                            addQuestion();
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>