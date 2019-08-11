document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".modal-toggle")
        .forEach(toggler => {
            toggler.addEventListener("click", evt => {
                evt.preventDefault();
                document.getElementById(toggler.dataset['target'])
                    .classList.toggle("active");
            })
        })
});

