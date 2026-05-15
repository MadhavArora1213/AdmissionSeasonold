<?php
require_once '../AdmissionSeason/admin/includes/db.php';

$page_title = "AI College Counselor | AdmissionSeason";

// AJAX handler — detect by POST + JSON content type OR X-Requested-With header
$isAjax = $_SERVER['REQUEST_METHOD'] === 'POST' && (
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
);

if ($isAjax) {
    header('Content-Type: application/json');

    try {
        $input   = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');

        if (!$message) {
            echo json_encode(['text' => 'Please type a message!', 'colleges' => []]);
            exit;
        }

        $colleges = [];
        $ml = strtolower($message);

        // ─── Keyword-based smart routing ─────────────────────────────
        if (strpos($ml, 'iit') !== false) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE type = 'CENTRAL' ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "IITs are the pinnacle of engineering education in India! 🎓 To get into an IIT you'll need a strong JEE Advanced rank (typically top 10,000). Here are some top Central / IIT-level institutions from our database:";

        } elseif (strpos($ml, 'nit') !== false) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE type = 'GOVERNMENT' ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "NITs are excellent choices for engineering! 🏛️ You can get into an NIT via JEE Main ranks. Here are some top Government institutions:";

        } elseif (preg_match('/\b(engineering|b\.?tech|btech|cse|mechanical|civil|electrical)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE type IN ('GOVERNMENT','CENTRAL') ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "Great choice! Engineering is one of the most popular streams. 🔧 Entrance exams: JEE Main, JEE Advanced, BITSAT, VITEEE. Here are some top engineering colleges:";

        } elseif (preg_match('/\b(mbbs|medical|neet|doctor|bds|medicine)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "For MBBS/Medical, NEET UG is mandatory! 🏥 The competition is tough — aim for 600+ in NEET for government medical colleges. Here are some top medical/general colleges:";

        } elseif (preg_match('/\b(mba|management|cat|xat|mat|pgdm)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges ORDER BY RAND() LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "MBA is a great career accelerator! 📈 Top exams: CAT, XAT, GMAT, MAT. IIMs are the gold standard. Here are some management institutions:";

        } elseif (preg_match('/\b(delhi|new delhi)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE city IN ('Delhi','New Delhi') ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "Delhi has some of India's finest institutions! 🏙️ Great for placements and networking. Here are top colleges in Delhi:";

        } elseif (preg_match('/\b(mumbai|pune|maharashtra)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE state = 'Maharashtra' ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "Maharashtra has world-class institutions! 🌆 Mumbai & Pune are top choices for placements. Here are some colleges:";

        } elseif (preg_match('/\b(bangalore|bengaluru|karnataka)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE state = 'Karnataka' ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "Bangalore — India's Silicon Valley! 💻 Excellent for tech/engineering careers. Here are top colleges in Karnataka:";

        } elseif (preg_match('/\b(scholarship|fees|budget|cheap|affordable|low fee)\b/', $ml)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE type = 'GOVERNMENT' ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "Budget is very important! 💰 Government colleges typically charge ₹30K–₹1.5L/year vs ₹1–20L for private. Here are some affordable top government colleges:";

        } elseif (preg_match('/\b(hi|hello|hey|namaste|hii)\b/', $ml)) {
            $response = "Namaste! 🙏 I'm your AI College Counselor. I can help you find the perfect college based on:\n\n1. 🎓 Your target course (B.Tech, MBBS, MBA, etc.)\n2. 📍 Your preferred city/state\n3. 💰 Your budget or entrance exam scores\n\nJust tell me what you're looking for!";

        } else {
            // General fallback — show top ranked colleges
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges WHERE nirf_rank IS NOT NULL ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = "I'd love to help you find the right college! 🎯 Could you share:\n\n• Which course you want to study?\n• Your preferred city or state?\n• Your budget or exam score?\n\nIn the meantime, here are our top-ranked colleges:";
        }

        // Fallback if no colleges matched from DB
        if (empty($colleges)) {
            $stmt = $pdo->query("SELECT id, name, city, type FROM colleges ORDER BY nirf_rank ASC LIMIT 3");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode(['text' => $response, 'colleges' => $colleges]);

    } catch (Exception $e) {
        echo json_encode(['text' => 'Sorry, something went wrong on our end. Please try again!', 'colleges' => []]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Chat with our AI Counselor to get hyper-personalized college recommendations based on your marks, budget, and location.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (v4) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        
        .chat-container {
            height: calc(100vh - 180px);
            max-height: 800px;
        }

        @media (max-width: 768px) {
            .chat-container {
                height: calc(100vh - 130px);
                max-height: none;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            .flex-1.pt-16 {
                padding: 16px 0 0 0;
            }
        }
        
        .message-enter {
            animation: slideUp 0.3s ease-out forwards;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Typing indicator dots */
        .typing-dot {
            animation: typing 1.4s infinite ease-in-out both;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
    </style>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter'] min-h-screen flex flex-col">

<?php include 'includes/navbar.php'; ?>

<div class="flex-1 pt-16 flex justify-center items-center p-4">
    <div class="w-full max-w-4xl glass rounded-3xl border border-[var(--border)] overflow-hidden flex flex-col chat-container shadow-2xl shadow-indigo-500/10">
        
        <!-- Chat Header -->
        <div class="px-6 py-4 border-b border-[var(--border)] bg-white/[0.02] flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i data-lucide="bot" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-[var(--bg-card)] rounded-full"></div>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white leading-tight">AI Counselor</h1>
                    <p class="text-xs text-indigo-400 font-medium">Online • Llama 3.1 Powered</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button onclick="clearChat()" class="p-2 text-[var(--text-muted)] hover:text-white hover:bg-white/5 rounded-lg transition-colors" title="Clear Chat">
                    <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth">
            <!-- Welcome Message -->
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center mt-1">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                </div>
                <div class="max-w-[80%]">
                    <div class="bg-[var(--bg-secondary)] border border-[var(--border)] rounded-2xl rounded-tl-none p-4 text-sm text-[var(--text-primary)] leading-relaxed shadow-sm">
                        Hi there! 👋 I'm your personal AI Admissions Counselor. 
                        <br><br>
                        I can help you discover the perfect college based on your specific needs. Tell me about your:
                        <ul class="list-disc ml-5 mt-2 space-y-1 text-[var(--text-secondary)]">
                            <li>Target Course (e.g., B.Tech, MBA, MBBS)</li>
                            <li>Preferred Location (e.g., Delhi, Pune, Bangalore)</li>
                            <li>Budget or Entrance Exam Scores</li>
                        </ul>
                    </div>
                    <span class="text-[10px] text-[var(--text-muted)] mt-1.5 ml-1 inline-block">Just now</span>
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="p-4 border-t border-[var(--border)] bg-white/[0.01]">
            <form id="chatForm" class="relative flex items-end gap-2">
                <div class="relative flex-1 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-2xl overflow-hidden focus-within:border-indigo-500/50 transition-colors">
                    <textarea 
                        id="messageInput" 
                        rows="1"
                        placeholder="Type your message... (e.g. 'I want to do engineering in Delhi')" 
                        class="w-full bg-transparent text-sm text-white placeholder-[var(--text-muted)] p-4 pr-12 resize-none outline-none max-h-32 min-h-[56px] overflow-y-auto"
                        onkeydown="if(event.keyCode===13 && !event.shiftKey) { event.preventDefault(); submitChat(); }"
                    ></textarea>
                    <button type="button" class="absolute right-3 bottom-3 p-1.5 text-[var(--text-muted)] hover:text-indigo-400 transition-colors">
                        <i data-lucide="mic" class="w-5 h-5"></i>
                    </button>
                </div>
                <button type="submit" class="w-14 h-[56px] flex-shrink-0 bg-indigo-500 hover:bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 transition-all hover:scale-105 active:scale-95">
                    <i data-lucide="send" class="w-5 h-5 ml-1"></i>
                </button>
            </form>
            <div class="text-center mt-3">
                <span class="text-[10px] text-[var(--text-muted)]">
                    AI can make mistakes. Please verify important admission deadlines independently.
                </span>
            </div>
        </div>
        
    </div>
</div>

<script>
  lucide.createIcons();

  const chatMessages = document.getElementById('chatMessages');
  const chatForm = document.getElementById('chatForm');
  const messageInput = document.getElementById('messageInput');

  // Auto-resize textarea
  messageInput.addEventListener('input', function() {
      this.style.height = '56px';
      this.style.height = (this.scrollHeight) + 'px';
  });

  chatForm.addEventListener('submit', function(e) {
      e.preventDefault();
      submitChat();
  });

  function submitChat() {
      const text = messageInput.value.trim();
      if (!text) return;

      // Add user message
      addMessage(text, 'user');
      messageInput.value = '';
      messageInput.style.height = '56px';
      
      // Add loading indicator
      const loadingId = addLoadingIndicator();

      // Send to backend
      fetch('ai-counselor.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ message: text })
      })
      .then(res => res.json())
      .then(data => {
          document.getElementById(loadingId).remove();
          addMessage(data.text, 'ai', data.colleges);
      })
      .catch(err => {
          document.getElementById(loadingId).remove();
          addMessage("Sorry, I'm having trouble connecting to the server right now. Please try again.", 'ai');
      });
  }

  function addMessage(text, sender, colleges = null) {
      const isUser = sender === 'user';
      const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
      
      let html = '';
      if (isUser) {
          html = `
          <div class="flex gap-4 justify-end message-enter">
              <div class="max-w-[80%] flex flex-col items-end">
                  <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none p-4 text-sm leading-relaxed shadow-md">
                      ${escapeHtml(text)}
                  </div>
                  <span class="text-[10px] text-[var(--text-muted)] mt-1.5 mr-1 inline-block">${time}</span>
              </div>
          </div>`;
      } else {
          let collegesHtml = '';
          if (colleges && colleges.length > 0) {
              collegesHtml = '<div class="mt-4 space-y-2">';
              colleges.forEach(col => {
                  collegesHtml += `
                  <a href="college.php?id=${col.id}" class="block p-3 rounded-xl bg-[var(--bg-primary)] border border-[var(--border)] hover:border-indigo-500/50 transition-colors group">
                      <div class="font-semibold text-white group-hover:text-indigo-400 transition-colors text-sm">${escapeHtml(col.name)}</div>
                      <div class="text-xs text-[var(--text-muted)] mt-1 flex items-center gap-1">
                          <i data-lucide="map-pin" class="w-3 h-3"></i> ${escapeHtml(col.city)} • ${escapeHtml(col.type)}
                      </div>
                  </a>`;
              });
              collegesHtml += '</div>';
          }
          
          // Format text (convert newlines to br)
          const formattedText = text.replace(/\n/g, '<br>');

          html = `
          <div class="flex gap-4 message-enter">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center mt-1 shadow-md">
                  <i data-lucide="bot" class="w-4 h-4 text-white"></i>
              </div>
              <div class="max-w-[80%]">
                  <div class="bg-[var(--bg-secondary)] border border-[var(--border)] rounded-2xl rounded-tl-none p-4 text-sm text-[var(--text-primary)] leading-relaxed shadow-sm">
                      ${formattedText}
                      ${collegesHtml}
                  </div>
                  <span class="text-[10px] text-[var(--text-muted)] mt-1.5 ml-1 inline-block">${time}</span>
              </div>
          </div>`;
      }

      chatMessages.insertAdjacentHTML('beforeend', html);
      chatMessages.scrollTop = chatMessages.scrollHeight;
      lucide.createIcons();
  }

  function addLoadingIndicator() {
      const id = 'loading-' + Date.now();
      const html = `
      <div id="${id}" class="flex gap-4 message-enter">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center mt-1">
              <i data-lucide="bot" class="w-4 h-4 text-white"></i>
          </div>
          <div class="bg-[var(--bg-secondary)] border border-[var(--border)] rounded-2xl rounded-tl-none px-5 py-4">
              <div class="flex gap-1">
                  <div class="w-2 h-2 bg-indigo-500 rounded-full typing-dot"></div>
                  <div class="w-2 h-2 bg-indigo-500 rounded-full typing-dot"></div>
                  <div class="w-2 h-2 bg-indigo-500 rounded-full typing-dot"></div>
              </div>
          </div>
      </div>`;
      chatMessages.insertAdjacentHTML('beforeend', html);
      chatMessages.scrollTop = chatMessages.scrollHeight;
      lucide.createIcons();
      return id;
  }

  function clearChat() {
      if(confirm('Clear conversation history?')) {
          const firstMsg = chatMessages.firstElementChild.outerHTML;
          chatMessages.innerHTML = firstMsg;
      }
  }

  function escapeHtml(unsafe) {
      return unsafe
           .replace(/&/g, "&amp;")
           .replace(/</g, "&lt;")
           .replace(/>/g, "&gt;")
           .replace(/"/g, "&quot;")
           .replace(/'/g, "&#039;");
  }

  // Handle URL param if redirecting from college page
  window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const collegeParam = urlParams.get('college');
      if (collegeParam) {
          messageInput.value = "I need help with admission for " + collegeParam;
          setTimeout(() => submitChat(), 500);
      }
  });
</script>
</body>
</html>
