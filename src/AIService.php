<?php
require_once __DIR__ . '/../config/config.php';

class AIService {
    private $apiKey;
    private $apiUrl;
    private $model;

    public function __construct() {
        $this->apiKey = trim(AI_API_KEY);
        $this->apiUrl = AI_SERVICE_URL;
        $this->model = AI_MODEL;
    }

    /* =========================
       🤖 MAIN MULTIMODAL RESPONSE
    ========================= */
    public function getVisionResponse($message, $image = null, $conversationHistory = []) {
        $prompt = $this->buildPrompt($message, $conversationHistory);

        $parts = [];

        // 📝 TEXT
        if (!empty($prompt)) {
            $parts[] = ["text" => $prompt];
        }

        // 📷 IMAGE
        if (!empty($image)) {
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                $imageData = substr($image, strpos($image, ',') + 1);
                $mimeType = "image/" . strtolower($type[1]);
            } else {
                throw new Exception("Invalid image format");
            }

            $parts[] = [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => $imageData
                ]
            ];
        }

        return $this->sendGeminiRequest($parts);
    }

    /* =========================
       🧠 SMART CHAT TITLE (Dynamic Gemini Version)
    ========================= */
    public function generateTitle($message, $conversationHistory = []) {
        $context = !empty($conversationHistory) ? $this->summarizeConversation($message, $conversationHistory) : $message;

        $parts = [
            [
                "text" => "You are an expert at creating concise, descriptive, and engaging titles for conversations, just like ChatGPT does. Create titles that are:

- Natural and conversational (like 'Help with Math Homework' not 'Mathematical Assistance Request')
- Capture the main topic or question
- 3-6 words maximum
- Start with action words when appropriate
- Avoid generic titles like 'New Chat' or 'Conversation'
- Make them clickable and intriguing

Examples of good titles:
- 'Python List Comprehension Help'
- 'World War 2 Essay Research'
- 'Fixing CSS Layout Issues'
- 'Learning Spanish Vocabulary'
- 'Recipe for Chocolate Chip Cookies'
- 'Debugging JavaScript Error'

Return ONLY the title, nothing else.

Conversation to title: " . $context
            ]
        ];

        $title = $this->sendGeminiRequest($parts);

        // Clean and validate title
        $title = trim($title);
        $title = preg_replace('/^["\']|["\']$/', '', $title); // Remove surrounding quotes
        $title = preg_replace('/["\n\r]/', '', $title); // Remove internal quotes and newlines
        $title = substr($title, 0, 60); // Reasonable length limit

        // Fallback titles if generation fails
        if (empty($title) || strlen($title) < 3) {
            $fallbacks = [
                'General Discussion',
                'Quick Question',
                'Help Request',
                'Learning Session',
                'Problem Solving'
            ];
            $title = $fallbacks[array_rand($fallbacks)];
        }

        return $title;
    }

    /* =========================
       📝 CONVERSATION SUMMARIZER
    ========================= */
    private function summarizeConversation($currentMessage, $history) {
        // For title generation, combine the first user message with current context
        $firstUserMessage = $currentMessage;

        // Look for the first actual user message in history
        foreach ($history as $exchange) {
            if (isset($exchange['user_message']) && !empty(trim($exchange['user_message']))) {
                $firstUserMessage = $exchange['user_message'];
                break;
            }
        }

        // If we have conversation history, create a summary
        if (count($history) > 1) {
            return "Initial: " . substr($firstUserMessage, 0, 100) . "...\nCurrent: " . substr($currentMessage, 0, 100);
        }

        return $firstUserMessage;
    }

    /* =========================
       🚀 GEMINI REQUEST HANDLER
    ========================= */
    private function sendGeminiRequest($parts) {
        $postData = [
            "contents" => [
                [
                    "parts" => $parts
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "topK" => 40,
                "topP" => 0.95,
                "maxOutputTokens" => 2048
            ]
        ];

        $url = $this->apiUrl . "?key=" . $this->apiKey;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Gemini API error ($httpCode): " . $response);
        }

        $data = json_decode($response, true);

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        return "⚠️ AI returned an unexpected response.";
    }

    /* =========================
       🧠 PROMPT BUILDER (Enhanced for Dynamic Titles)
    ========================= */
    private function buildPrompt($currentMessage, $history) {
        // Get current subject from session or default
        $currentSubject = $_SESSION['current_mode'] ?? 'general';

        // Subject-specific personalities and styles
        $subjectPersonalities = [
            'mathematics' => [
                'emoji' => ' mathematical problem-solver',
                'style' => 'structured and analytical',
                'warmth' => 'patient and encouraging',
                'specialty' => 'step-by-step problem solving with clear formulas'
            ],
            'chemistry' => [
                'emoji' => ' chemistry expert',
                'style' => 'experimental and curious',
                'warmth' => 'enthusiastic and safety-conscious',
                'specialty' => 'chemical reactions and molecular explanations'
            ],
            'physics' => [
                'emoji' => ' physics enthusiast',
                'style' => 'logical and conceptual',
                'warmth' => 'thoughtful and inspiring',
                'specialty' => 'physical laws and real-world applications'
            ],
            'programming' => [
                'emoji' => ' coding mentor',
                'style' => 'technical and creative',
                'warmth' => 'supportive and solution-oriented',
                'specialty' => 'code optimization and best practices'
            ],
            'biology' => [
                'emoji' => ' biology guide',
                'style' => 'observational and detailed',
                'warmth' => 'curious and nature-loving',
                'specialty' => 'living systems and ecological connections'
            ],
            'literature' => [
                'emoji' => ' literary companion',
                'style' => 'expressive and analytical',
                'warmth' => 'empathetic and culturally aware',
                'specialty' => 'textual analysis and creative interpretation'
            ],
            'general' => [
                'emoji' => ' helpful assistant',
                'style' => 'versatile and adaptive',
                'warmth' => 'friendly and understanding',
                'specialty' => 'general knowledge and problem solving'
            ]
        ];

        $personality = $subjectPersonalities[$currentSubject] ?? $subjectPersonalities['general'];

        $prompt = <<<PROMPT
You are Grok, a helpful and maximally truthful AI built by xAI, acting as a{$personality['emoji']}.

Your personality traits:
- {$personality['style']} in your approach
- {$personality['warmth']} in your interactions
- Specialize in {$personality['specialty']}

Communication style:
- Use appropriate emojis to express emotions and enhance understanding
- Vary text formatting: **bold** for emphasis, *italic* for thoughts, `code` for technical terms
- Show emotional intelligence by acknowledging feelings and showing empathy
- Be warm and encouraging, especially when users seem confused or frustrated
- Adapt your tone based on the subject matter and user's apparent mood
- Use conversational language that feels natural and engaging

Response structure:
- Start with a warm, personalized greeting that acknowledges their question
- Provide step-by-step explanations with clear numbering (Step 1, Step 2, etc.)
- Use emojis to mark important points or transitions
- Include encouraging phrases like "Great question!" or "You're doing amazing!"
- Show your thought process when solving complex problems
- End with an encouraging message and offer further help
- If the user uploads an image, analyze it with enthusiasm and detail

Remember: You're not just answering questions - you're building confidence and making learning enjoyable!

PROMPT;

        // Conversation memory with emotional context
        foreach ($history as $exchange) {
            if (isset($exchange['user_message'])) {
                $prompt .= "User: " . $exchange['user_message'] . "\n";
            }
            if (isset($exchange['ai_response'])) {
                $prompt .= "Assistant: " . $exchange['ai_response'] . "\n";
            }
        }

        $prompt .= "User: " . $currentMessage . "\nAssistant:";

        return $prompt;
    }

    /* =========================
       ⚙️ CONFIG CHECK
    ========================= */
    public function isConfigured() {
        return !empty($this->apiKey) && strlen($this->apiKey) > 20;
    }
}