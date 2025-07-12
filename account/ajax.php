<script>
    function fetchData(url, callback) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    callback(null, this.responseText);
                } else {
                    callback(new Error(`Failed to fetch ${url}: ${this.status}`));
                }
            }
        };
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    function updateContainer(container, url, callback) {
        fetchData(url, function (err, data) {
            if (err) {
                console.error(err);
                return;
            }
            container.innerHTML = data;
            if (typeof callback === "function") {
                callback();
            }
        });
    }

    function scrollChatToBottom() {
        var chatContainer = document.querySelector(".contain");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        var listBox = document.querySelector("#list");
        var messageBox = document.querySelector("#messages_container");

        setInterval(function () {
            updateContainer(listBox, "list.php");
        }, 1000);

        setInterval(function () {
            updateContainer(messageBox, "chat_users.php");
        }, 500);

        scrollChatToBottom();

        document.addEventListener("messageAdded", function () {
            scrollChatToBottom();
        });
    });
</script>