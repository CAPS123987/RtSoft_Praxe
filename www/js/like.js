document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('like-btn');
    if (!likeBtn) return;

    likeBtn.addEventListener('click', async function () {
        if (likeBtn.disabled) return;

        if (likeBtn.dataset.loggedIn !== 'true') {
            window.toast.error('Pro lajkování se musíte přihlásit.');
            return;
        }

        const url = likeBtn.dataset.url;

        try {
            likeBtn.disabled = true;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Chyba při komunikaci se serverem.');
            }

            const data = await response.json();

            if (data.error) {
                window.toast.error(data.error);
                return;
            }

            const liked = data.liked;
            const likeCount = data.likeCount;

            likeBtn.dataset.liked = liked ? 'true' : 'false';

            const icon = likeBtn.querySelector('.like-icon');
            const count = likeBtn.querySelector('.like-count');

            icon.textContent = liked ? '\u2764' : '\u2661';
            count.textContent = likeCount;

            if (liked) {
                likeBtn.classList.remove('border-stone-700', 'bg-stone-800', 'text-stone-400');
                likeBtn.classList.add('border-red-500', 'bg-red-500/20', 'text-red-400');
            } else {
                likeBtn.classList.remove('border-red-500', 'bg-red-500/20', 'text-red-400');
                likeBtn.classList.add('border-stone-700', 'bg-stone-800', 'text-stone-400');
            }
        } catch (e) {
            console.error('Like error:', e);
        } finally {
            likeBtn.disabled = false;
        }
    });
});

