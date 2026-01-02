<?php
require_once '../../config/database.php';
require_once '../partials/header.php';
require_once '../partials/nav_student.php';


$quizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;
?>

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div id="initialLoader" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Chargement du quiz...</p>
    </div>

    <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        <p class="font-bold">Erreur</p>
        <p id="errorText"></p>
        <a href="categories.php" class="text-blue-600 hover:underline mt-2 inline-block">
            Retour aux catégories
        </a>
    </div>

    <div id="quizContainer" class="hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 id="quizTitle" class="text-2xl font-bold text-gray-900"></h1>
                <span id="categoryBadge"
                    class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full"></span>
            </div>
            <p id="quizDescription" class="text-gray-600 mb-4"></p>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Question <span id="currentQuestion">1</span> sur <span id="totalQuestions">0</span></span>
                    <span id="timer" class="font-semibold">00:00</span>
                </div>
            </div>
        </div>

        <div id="questionsContainer" class="space-y-6"></div>

        <div class="flex justify-between mt-8">
            <button id="prevBtn" onclick="previousQuestion()"
                class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                <i class="fas fa-arrow-left mr-2"></i>Précédent
            </button>

            <button id="nextBtn" onclick="nextQuestion()"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                Suivant<i class="fas fa-arrow-right ml-2"></i>
            </button>

            <button id="submitBtn" onclick="submitQuiz()"
                class="hidden px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-check mr-2"></i>Soumettre
            </button>
        </div>
    </div>

    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmer la soumission</h3>
            <p class="text-gray-600 mb-6">Vous etes sur de vouloir soumettre vos réponses? Vous ne pourrez plus les
                modifier.</p>
            <div class="flex gap-4">
                <button onclick="closeConfirmModal()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                    Annuler
                </button>
                <button onclick="confirmSubmit()"
                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    Confirmer
                </button>
            </div>
        </div>
    </div>

    <div id="scoreModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-auto">
        <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Résultat du quiz</h3>
            <p class="text-gray-700 mb-4">
                Votre score : <span id="scoreValue" class="font-semibold"></span>
            </p>
            <div id="resultsContainer" class="space-y-4 max-h-96 overflow-y-auto"></div>
            <button onclick="closeScoreModal()"
                class="mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
    const quizId = <?= $quizId ?>;
    let quizData = null;
    let currentQuestionIndex = 0;
    let userAnswers = {};
    let startTime = null;
    let timerInterval = null;

    async function loadQuiz() {
        const initialLoader = document.getElementById('initialLoader');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const quizContainer = document.getElementById('quizContainer');

        try {
            const response = await fetch(`../../actions/student/take_quiz.php?quiz_id=${quizId}`);

            const result = await response.json();

            quizData = result;
            startTime = new Date();

            initialLoader.classList.add('hidden');
            quizContainer.classList.remove('hidden');

            renderQuizInfo();
            renderQuestions();
            showQuestion(0);
            startTimer();

        } catch (error) {
            initialLoader.classList.add('hidden');
            errorText.textContent = error.message;
            errorMessage.classList.remove('hidden');
        }
    }

    function renderQuizInfo() {
        document.getElementById('quizTitle').textContent = quizData.quiz.titre;
        document.getElementById('quizDescription').textContent = quizData.quiz.description || '';
        document.getElementById('categoryBadge').textContent = quizData.quiz.categorie_nom;
        document.getElementById('totalQuestions').textContent = quizData.total_questions;
    }

    function renderQuestions() {
        const container = document.getElementById('questionsContainer');

        container.innerHTML = quizData.questions.map((q, index) => `
            <div id="question-${index}" class="question-card bg-white rounded-lg shadow-md p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    ${escapeHtml(q.question)}
                </h3>
                <div class="space-y-3">
                    ${Object.entries(q.options).map(([key, value]) => `
                        <label class="option-label flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all hover:border-blue-400 hover:bg-blue-50">
                            <input type="radio" 
                                   name="question-${q.id}" 
                                   value="${key}"
                                   onchange="selectAnswer(${q.id}, ${key})"
                                   class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">${escapeHtml(value)}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    function showQuestion(index) {
        document.querySelectorAll('.question-card').forEach(card => {
            card.classList.add('hidden');
        });

        document.getElementById(`question-${index}`).classList.remove('hidden');
        currentQuestionIndex = index;

        updateNavigationButtons();
        const currentQuestion = quizData.questions[index];
        if (userAnswers[currentQuestion.id]) {
            const radio = document.querySelector(`input[name="question-${currentQuestion.id}"][value="${userAnswers[currentQuestion.id]}"]`);
            if (radio) radio.checked = true;
        }
    }

    function selectAnswer(questionId, optionValue) {
        userAnswers[questionId] = optionValue;

        const questionCard = document.querySelector(`input[name="question-${questionId}"]`).closest('.question-card');
        questionCard.querySelectorAll('.option-label').forEach(label => {
            label.classList.remove('border-blue-600', 'bg-blue-50');
        });

        const selectedLabel = document.querySelector(`input[name="question-${questionId}"][value="${optionValue}"]`).closest('.option-label');
        selectedLabel.classList.add('border-blue-600', 'bg-blue-50');
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn.disabled = currentQuestionIndex === 0;

        if (currentQuestionIndex === quizData.total_questions - 1) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    function previousQuestion() {
        if (currentQuestionIndex > 0) {
            showQuestion(currentQuestionIndex - 1);
        }
    }

    function nextQuestion() {
        if (currentQuestionIndex < quizData.total_questions - 1) {
            showQuestion(currentQuestionIndex + 1);
        }
    }

    function startTimer() {
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((new Date() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const seconds = (elapsed % 60).toString().padStart(2, '0');
            document.getElementById('timer').textContent = `${minutes}:${seconds}`;
        }, 1000);
    }

    function submitQuiz() {
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    async function confirmSubmit() {
        closeConfirmModal();
        clearInterval(timerInterval);

        try {
            const response = await fetch('../../actions/student/submit_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ quiz_id: quizId, answers: userAnswers })
            });

            const result = await response.json();

            if (result.success) {
                showScoreModal(result);
            } else {
                alert('Erreur: ' + result.error);
            }

        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la soumission');
        }
    }

    function showScoreModal(result) {
        document.getElementById('scoreModal').classList.remove('hidden');

        const scoreValue = `${result.score} / ${result.total_questions} (${result.percentage}%)`;
        document.getElementById('scoreValue').textContent = scoreValue;

        const container = document.getElementById('resultsContainer');
        container.innerHTML = result.details.map(q => {
            const correctClass = q.is_correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
            const icon = q.is_correct ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600';
            return `
                <div class="border-2 ${correctClass} rounded-lg p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">${escapeHtml(q.question)}</h4>
                            <p class="text-sm text-gray-600">
                                Votre réponse : <span class="${q.is_correct ? 'text-green-600' : 'text-red-600'}">
                                ${escapeHtml(q.options[q.user_answer] || 'Aucune')}</span>
                            </p>
                            ${!q.is_correct ? `<p class="text-sm text-gray-600">Réponse correcte : <span class="text-green-600">
                                ${escapeHtml(q.options[q.correct_answer])}</span></p>` : ''}
                        </div>
                        <i class="fas ${icon} text-2xl"></i>
                    </div>
                </div>
            `;
        }).join('');
    }

    function closeScoreModal() {
        document.getElementById('scoreModal').classList.add('hidden');
        window.location.href = 'dashboard.php';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }

    document.addEventListener('DOMContentLoaded', loadQuiz);

    window.addEventListener('beforeunload', (e) => {
        if (quizData && Object.keys(userAnswers).length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
<?php require_once '../partials/footer.php'; ?>