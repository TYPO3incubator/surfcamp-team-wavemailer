
class NewsletterCard extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        const header = this.querySelector('.newsletter-header');
        const content = this.querySelector('.newsletter-content');
        
        if (header && content) {
            header.addEventListener('click', () => {
                const isActive = content.classList.contains('active');
                
                if (isActive) {
                    content.classList.remove('active');
                    header.classList.remove('active');
                } else {
                    content.classList.add('active');
                    header.classList.add('active');
                }
            });
        }
    }
}

if (!customElements.get('wave-mailer-newsletter-card')) {
    customElements.define('wave-mailer-newsletter-card', NewsletterCard);
}
