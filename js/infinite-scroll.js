document.addEventListener('DOMContentLoaded', function () {
    let page = 2; 
    let loading = false;
    const postWrapper = document.getElementById('post-wrapper');
    const loadingIndicator = document.getElementById('loading');

    if (!postWrapper || !loadingIndicator) {
        console.error('Required DOM elements are missing');
        return;
    }

    const loadMorePosts = () => {
        if (loading) return;
        loading = true;
        loadingIndicator.style.display = 'block';

        fetch(`${jmThemeData.ajaxurl}?action=load_more_posts&page=${page}`)
            .then((response) => response.text())
            .then((data) => {
                if (data.trim() === '') {
                    // No more posts
                    window.removeEventListener('scroll', scrollHandler);
                } else {
                    postWrapper.insertAdjacentHTML('beforeend', data);
                    page++;
                }
            })
            .catch((error) => console.error('Error loading more posts:', error))
            .finally(() => {
                loading = false;
                loadingIndicator.style.display = 'none';
            });
    };

    const scrollHandler = () => {
        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            loadMorePosts();
        }
    };

    window.addEventListener('scroll', scrollHandler);
});
