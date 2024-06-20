<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual Círculo de Suboficiales</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="title">Círculo Online</div>
        <div class="form">
            <div class="bot-inbox inbox">
                <div class="icon">
                    <img src="img/logo.gif" alt="Logo" width="50px">
                </div>
                <div class="msg-header">
                    <p>Hola, Soy tu asistente virtual, ¿Cómo puedo ayudarte?</p>
                </div>
            </div>
        </div>
        <div class="typing-field">
            <div class="input-data">
                <input id="data" type="text" placeholder="Escriba su Consulta." required>
                <button id="send-btn">Enviar</button>
                <button id="voice-btn">🎤</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'es-ES';

            recognition.onresult = function (event) {
                const transcript = event.results[0][0].transcript;
                $("#data").val(transcript);
                $("#send-btn").click();
            };

            $("#voice-btn").on("click", function () {
                recognition.start();
            });

            $("#send-btn").on("click", function () {
                var $value = $("#data").val();
                if ($value.trim() === "") {
                    if (!$(".form .bot-inbox.warning").length) {
                        var $warningMsg = '<div class="bot-inbox inbox warning"><div class="icon"><img src="img/logo.gif" alt="Logo" width="50px"></div><div class="msg-header"><p>Por favor, escriba su consulta en el cuadro de búsqueda.</p></div></div>';
                        $(".form").append($warningMsg);
                        $(".form").scrollTop($(".form")[0].scrollHeight);
                    }
                } else {
                    $(".form .bot-inbox.warning").remove(); // Remove the warning message if it exists
                    var $msg = '<div class="user-inbox inbox"><div class="msg-header"><p>' + $value + '</p></div></div>';
                    $(".form").append($msg);
                    $("#data").val('');

                    // iniciar el código ajax
                    $.ajax({
                        url: 'message.php',
                        type: 'POST',
                        data: 'text=' + $value,
                        success: function (result) {
                            var $replay = '<div class="bot-inbox inbox"><div class="icon"><img src="img/logo.gif" alt="Logo" width="50px"></div><div class="msg-header"><p>' + result + '</p></div></div>';
                            $(".form").append($replay);
                            // cuando el chat baja, la barra de desplazamiento llega automáticamente al final
                            $(".form").scrollTop($(".form")[0].scrollHeight);

                            // Convertir la respuesta de texto a voz
                            const utterance = new SpeechSynthesisUtterance(result);
                            utterance.lang = 'es-ES';
                            window.speechSynthesis.speak(utterance);
                        }
                    });
                }
            });

            // Permitir el envío al presionar Enter
            $("#data").on("keypress", function (event) {
                if (event.which == 13) {
                    $("#send-btn").click();
                }
            });
        });
    </script>

</body>

</html>
