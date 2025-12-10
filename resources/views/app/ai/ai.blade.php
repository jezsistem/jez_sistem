<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - Dark Mode</title>
    <style>
        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #0d0d0d;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* HEADER */
        .header {
            padding: 15px 20px;
            background: #111;
            border-bottom: 1px solid #222;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }

        /* CHAT AREA */
        .chat-area {
            flex: 1;
            overflow-y: auto;
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            max-width: 70%;
            padding: 15px 18px;
            border-radius: 14px;
            font-size: 15px;
            line-height: 1.6;
            animation: fadeIn 0.2s ease-in-out;
        }

        /* AI Message */
        .bot {
            background: #1a1a1a;
            border-left: 4px solid #cc0000; /* garis merah elegan */
            color: #f5f5f5;
            border-radius: 10px 10px 10px 4px;
        }

        /* User Message */
        .user {
            margin-left: auto;
            background: #cc0000;
            color: white;
            border-radius: 10px 10px 4px 10px;
        }

        /* INPUT AREA */
        .input-box {
            padding: 18px;
            display: flex;
            gap: 10px;
            background: #111;
            border-top: 1px solid #222;
        }

        .input-box textarea {
            flex: 1;
            resize: none;
            padding: 12px;
            font-size: 15px;
            border-radius: 10px;
            background: #1a1a1a;
            color: white;
            border: 1px solid #333;
            outline: none;
            height: 55px;
        }

        .input-box textarea:focus {
            border-color: #cc0000;
        }

        .send-btn {
            background: #cc0000;
            border: none;
            padding: 0 25px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .send-btn:hover {
            background: #a30000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

    </style>
</head>
<body>

<div class="header">AI Assistant – Retail Dashboard</div>

<div class="chat-area" id="chatBox">
    <div class="message bot">
        Halo! Saya siap membantu analisis penjualan, stok, PO, fast moving, slow moving, dan data retail lainnya.
    </div>
</div>

<div class="input-box">
    <textarea id="message" placeholder="Ketik pesan..."></textarea>
    <button class="send-btn" onclick="sendMessage()">Kirim</button>
</div>

<script>
    async function sendMessage() {
        let text = document.getElementById("message").value;
        if (text.trim() === "") return;

        appendMessage(text, "user");

        document.getElementById("message").value = "";

        // Tampilkan typing dulu
        let loading = appendMessage("...", "bot");

        // Kirim ke Laravel → lalu ke Ollama
        const res = await fetch("/ai-process", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: text })
        });

        const data = await res.json();

        loading.remove();

        appendMessage(data.reply, "bot");
    }

    function appendMessage(text, sender) {
        let chatBox = document.getElementById("chatBox");
        let bubble = document.createElement("div");

        bubble.classList.add("message", sender);
        bubble.innerText = text;

        chatBox.appendChild(bubble);
        chatBox.scrollTop = chatBox.scrollHeight;

        return bubble;
    }
</script>

</body>
</html>
