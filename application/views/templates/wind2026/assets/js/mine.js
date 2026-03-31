(() => {
    'use strict';

    function refreshShoppingCartSection() {
        $.get(window.location.href, function (response) {
            const $html = $('<div>').html(response);
            const $newCart = $html.find('#shopping-cart');

            if ($newCart.length) {
                $('#shopping-cart').replaceWith($newCart);
            }

            const $sumOfItems = $html.find('.sumOfItems').first();
            if ($sumOfItems.length) {
                $('.sumOfItems').text($sumOfItems.text());
            }

            const $dropdownCart = $html.find('.dropdown-cart').first();
            if ($dropdownCart.length) {
                $('.dropdown-cart').html($dropdownCart.html());
            }
        });
    }

    function submitCartAdd(productId, onDone) {
        $.ajax({
            url: '/Projects/E-shop/index.php/home/manageShoppingCart',
            type: 'POST',
            data: { id: productId },
            success: function () {
                if (typeof onDone === 'function') onDone();
            }
        });
    }

    function submitCartRemove(productId, onDone) {
        $.ajax({
            url: '/Projects/E-shop/index.php/home/removeFromCart',
            type: 'GET',
            data: {
                'delete-product': productId,
                'back-to': 'shopping-cart'
            },
            success: function () {
                if (typeof onDone === 'function') onDone();
            }
        });
    }

    $(function () {
        $('.search-field-header').on('keydown', function (event) {
            if (event.which === 13 || event.key === 'Enter') {
                event.preventDefault();
                $('#bigger-search').submit();
            }
        });
    });

    $(document).on('click', '#show-xs-nav', function () {
        $('#nav-categories').stop(true, true).toggle('fast', function () {
            if ($(this).is(':visible')) {
                $('#show-xs-nav .hidde-sp').show();
                $('#show-xs-nav .show-sp').hide();
            } else {
                $('#show-xs-nav .hidde-sp').hide();
                $('#show-xs-nav .show-sp').show();
            }
        });
    });

    $(document).on('click', '[data-wind-toggle="dropdown"]', function (e) {
        e.preventDefault();
        const target = $(this).attr('data-wind-target');
        if (!target) return;

        const $panel = $(target);
        if (!$panel.length) return;

        $('[data-wind-dropdown-panel="true"]').not($panel).addClass('hidden');
        $panel.toggleClass('hidden');
    });

    $(document).on('click', '[data-wind-toggle="collapse"]', function (e) {
        e.preventDefault();
        const target = $(this).attr('data-wind-target');
        if (!target) return;
        $(target).toggleClass('hidden');
    });

    $(document).on('click', function (e) {
        const $t = $(e.target);
        if ($t.closest('[data-wind-dropdown="true"]').length) return;
        $('[data-wind-dropdown-panel="true"]').addClass('hidden');
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('[data-wind-dropdown-panel="true"]').addClass('hidden');
        }
    });

    if (!window.__wind2026_click_outside_bound) {
        window.__wind2026_click_outside_bound = true;
        document.addEventListener('pointerdown', function (e) {
            const target = e.target;
            if (target && target.closest && target.closest('[data-wind-dropdown="true"]')) {
                return;
            }
            document.querySelectorAll('[data-wind-dropdown-panel="true"]').forEach(function (el) {
                el.classList.add('hidden');
            });
        }, true);
    }

    $(document).on('click', '[data-wind-carousel-btn]', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const target = $btn.attr('data-wind-carousel-target');
        if (!target) return;

        const el = document.querySelector(target);
        if (!el) return;

        const dir = $btn.attr('data-wind-carousel-btn');
        const amount = Math.max(240, Math.floor(el.clientWidth * 0.9));
        el.scrollBy({ left: dir === 'prev' ? -amount : amount, behavior: 'smooth' });
    });

    $(document).off('click.cartPlus').on('click.cartPlus', '.cart-plus', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const $btn = $(this);
        if ($btn.hasClass('pointer-events-none') || $btn.data('loading')) return false;

        $btn.data('loading', true).addClass('opacity-50');

        submitCartAdd($btn.data('id'), function () {
            refreshShoppingCartSection();
            $btn.data('loading', false).removeClass('opacity-50');
        });

        return false;
    });

    $(document).off('click.cartMinus').on('click.cartMinus', '.cart-minus', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const $btn = $(this);
        if ($btn.data('loading')) return false;

        $btn.data('loading', true).addClass('opacity-50');

        submitCartRemove($btn.data('id'), function () {
            refreshShoppingCartSection();
            $btn.data('loading', false).removeClass('opacity-50');
        });

        return false;
    });

    $(document).off('click.cartDelete').on('click.cartDelete', '.cart-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const $btn = $(this);
        if ($btn.data('loading')) return false;

        $btn.data('loading', true).addClass('opacity-50');

        submitCartRemove($btn.data('id'), function () {
            refreshShoppingCartSection();
            $btn.data('loading', false).removeClass('opacity-50');
        });

        return false;
    });
})();