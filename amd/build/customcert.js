define([], function() {

    return {

        init: function(downloadurl, buttontext) {

            function replaceButton() {

                const form = document.querySelector(
                    'form[action*="/mod/customcert/view.php"] input[name="downloadown"]'
                );

                if (!form) {
                    return;
                }

                const singlebutton = form.closest('.singlebutton');

                if (!singlebutton) {
                    return;
                }

                singlebutton.innerHTML =
                    '<a href="' +
                    downloadurl +
                    '" class="btn btn-primary">' +
                    buttontext +
                    '</a>';
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', replaceButton);
            } else {
                replaceButton();
            }

        }

    };

});