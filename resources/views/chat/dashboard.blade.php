@extends('dashboard')

@section('content')
<div class="container">
    <h3>Chat with <span id="receiverName"></span></h3>

    <div class="chat-box" id="chatBox"></div> <!-- মেসেজ এখানে লোড হবে -->

    {{-- <h4>Connections</h4>
    <ul id="connectionsList"></ul> <!-- Connections List এখানে দেখানো হবে --> --}}

    <form id="messageForm">
        @csrf
        <textarea id="messageInput" name="message" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<style>
    .chat-box { 
        max-height: 400px; 
        overflow-y: auto; 
        padding: 10px; 
        border: 1px solid #ddd; 
        margin-bottom: 10px; 
        display: flex;
        flex-direction: column;
    }
    .sent { 
        text-align: right; 
        background: #dcf8c6; 
        padding: 10px; 
        border-radius: 10px; 
        margin-bottom: 5px; 
        align-self: flex-end;
    }
    .received { 
        text-align: left; 
        background: #f1f1f1; 
        padding: 10px; 
        border-radius: 10px; 
        margin-bottom: 5px; 
        align-self: flex-start;
    }
    textarea { width: 100%; height: 50px; }
    button { 
        padding: 10px 20px; 
        background-color: #007bff; 
        color: white; 
        border: none; 
        cursor: pointer; 
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        let receiverId = "{{ $receiver_id }}"; // Laravel থেকে Receiver ID

        function fetchData() {
            $.ajax({
                url: "/chat/" + receiverId, // API কল করা
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        let chatBox = $("#chatBox");
                        chatBox.html(""); // পুরানো মেসেজ মুছে ফেলা
                        $("#receiverName").text(response.receiver.name); // রিসিভারের নাম আপডেট করা

                        // মেসেজ লোড করা
                        response.messages.forEach(function (message) {
                            let messageClass = (message.sender_id == {{ auth()->id() }}) ? "sent" : "received";
                            let messageHtml = `
                                <div class="${messageClass}">
                                    <p>${message.message}</p>
                                    <span>${new Date(message.created_at).toLocaleString()}</span>
                                </div>
                            `;
                            chatBox.append(messageHtml);
                        });

                        // সর্বশেষ মেসেজে স্ক্রল করা
                        chatBox.scrollTop(chatBox.prop("scrollHeight"));

                       
                    }
                },
                error: function (xhr) {
                    console.log("Error fetching data:", xhr);
                }
            });
        }

        fetchData(); // প্রথমবার API কল করা
        setInterval(fetchData, 5000); // প্রতি 5 সেকেন্ড পর পর API কল করা

        // AJAX দিয়ে মেসেজ পাঠানো
        $("#messageForm").submit(function (e) {
            e.preventDefault(); // ডিফল্ট সাবমিট বন্ধ করা

            let message = $("#messageInput").val();

            $.ajax({
                url: "/chat/" + receiverId, 
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: message
                },
                success: function (response) {
                    if (response.status === "success") {
                        $("#messageInput").val(""); // ইনপুট ফিল্ড খালি করা
                        fetchData(); // নতুন মেসেজ লোড করা
                    }
                },
                error: function (xhr) {
                    console.log("Error sending message:", xhr);
                }
            });
        });
    });
</script>
@endsection
