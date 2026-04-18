<?php
require_once 'src/AIService.php';

try {
    $ai = new AIService();

    if (!$ai->isConfigured()) {
        echo "❌ AI service not configured. Please check your Gemini API key in .env file.\n";
        echo "Make sure AI_API_KEY is set with a valid Gemini API key (starts with 'AIza')\n";
        exit(1);
    }

    echo "✅ AI service loaded successfully\n";
    echo "🔄 Testing Gemini API connection...\n";

    // Test a simple request
    $response = $ai->getVisionResponse("Hello! Can you tell me what 2+2 equals?", null, []);
    echo "\n🤖 AI Response:\n" . $response . "\n\n";

    // Test title generation
    $title = $ai->generateTitle("I need help with basic math problems");
    echo "📝 Generated Title: " . $title . "\n";

    echo "✅ All tests passed! Your Gemini AI integration is working.\n";

} catch (Exception $e) {
    echo "❌ AI Error: " . $e->getMessage() . "\n";
    echo "\n🔧 Troubleshooting:\n";
    echo "1. Check your Gemini API key in .env file\n";
    echo "2. Make sure your API key starts with 'AIza'\n";
    echo "3. Verify you have credits in your Google AI account\n";
    echo "4. Check your internet connection\n";
}
?>