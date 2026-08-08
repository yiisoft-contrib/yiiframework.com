$(document).ready(function () {
    var themeToggle = document.querySelector('.theme-toggle');

    function updateThemeToggle() {
        if (!themeToggle) {
            return;
        }

        var dark = document.documentElement.classList.contains('theme-dark');
        themeToggle.setAttribute('aria-pressed', dark ? 'true' : 'false');
        themeToggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
    }

    if (themeToggle) {
        updateThemeToggle();
        themeToggle.addEventListener('click', function () {
            var dark = document.documentElement.classList.toggle('theme-dark');
            try {
                localStorage.setItem('yii-theme', dark ? 'dark' : 'light');
            } catch (error) {}
            updateThemeToggle();
        });
    }

    var heroPetals = document.querySelectorAll('.hero-petal[data-depth]');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    function backOut(progress) {
        var overshoot = 1.70158;
        return 1 + (overshoot + 1) * Math.pow(progress - 1, 3) + overshoot * Math.pow(progress - 1, 2);
    }

    function animateHeroPetals(startedAt) {
        var now = Date.now();
        var entranceProgress = Math.max(0, Math.min(1, (now - startedAt - 500) / 2000));
        var entrance = backOut(entranceProgress);
        var horizontal = Math.sin(now * .001) * .5;
        var vertical = Math.cos(now * .001) * .5;

        heroPetals.forEach(function (petal) {
            var depth = Number(petal.getAttribute('data-depth'));

            if (reducedMotion.matches) {
                petal.style.opacity = '.75';
                petal.style.filter = 'blur(8px)';
                petal.style.transform = 'none';
                return;
            }

            var x = horizontal * depth * 100;
            var y = vertical * depth * 100;
            var rotate = 90 * (1 - entrance);
            petal.style.opacity = String(.75 * entrance);
            petal.style.filter = 'blur(' + (4 + 4 * entrance) + 'px)';
            petal.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0) rotate(' + rotate + 'deg) scale(' + entrance + ')';
        });

        window.requestAnimationFrame(function () {
            animateHeroPetals(startedAt);
        });
    }

    if (heroPetals.length) {
        animateHeroPetals(Date.now());
    }

    var heroWord = document.querySelector('.hero-word[data-words]');

    if (heroWord) {
        var heroWords = JSON.parse(heroWord.getAttribute('data-words'));
        var heroWordIndex = 0;

        function typeHeroWord() {
            var word = heroWords[heroWordIndex][0];
            var className = heroWords[heroWordIndex][1];
            var character = 0;
            heroWord.className = 'hero-word ' + className;

            function typeCharacter() {
                character += 1;
                heroWord.textContent = word.slice(0, character);

                if (character < word.length) {
                    window.setTimeout(typeCharacter, 1000 / word.length);
                    return;
                }

                window.setTimeout(deleteCharacter, 2000);
            }

            function deleteCharacter() {
                character -= 1;
                heroWord.textContent = word.slice(0, character);

                if (character > 0) {
                    window.setTimeout(deleteCharacter, 1000 / word.length);
                    return;
                }

                heroWordIndex = (heroWordIndex + 1) % heroWords.length;
                typeHeroWord();
            }

            if (reducedMotion.matches) {
                heroWord.textContent = word;
                return;
            }

            typeCharacter();
        }

        typeHeroWord();
    }

    //SCROLLING
    $("a[href^='#']").on('click', function (e) {
        e.preventDefault();
        var hash = this.hash;
        if (hash != '') {
            $('html, body').animate({scrollTop: $(this.hash).offset().top}, 250, function () {
                window.location.hash = hash;
            });
        }
    });

    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            scrollDistance: 140, // Distance from top/bottom before showing element (px)
            scrollFrom: 'top', // 'top' or 'bottom'
            scrollSpeed: 300, // Speed back to top (ms)
            easingType: 'linear', // Scroll to top easing (see https://easings.net/)
            animation: 'fade', // Fade, slide, none
            animationInSpeed: 200, // Animation in speed (ms)
            animationOutSpeed: 200, // Animation out speed (ms)
            scrollText: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m10 3.293 6.707 6.707-1.414 1.414L11 7.121V17H9V7.121l-4.293 4.293L3.293 10 10 3.293Z"/></svg>',
            scrollTitle: 'Scroll to top', // Set a custom <a> title if required. Defaults to scrollText
            scrollImg: false, // Render the inline SVG supplied in scrollText
            activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            zIndex: 2147483647 // Z-Index for the overlay
        });
        $('#scrollUp').attr('aria-label', 'Scroll to top');
    });

    $(".version-selector .dropdown-menu:empty").hide();
});
