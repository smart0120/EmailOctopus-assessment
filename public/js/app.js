class MailingListApp {
    constructor() {
        this.apiBase = '/api/contacts';
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadContacts();
    }

    bindEvents() {
        // Add contact form
        const form = document.getElementById('addContactForm');
        form.addEventListener('submit', (e) => this.handleAddContact(e));

        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        refreshBtn.addEventListener('click', () => this.loadContacts());
    }

    async loadContacts() {
        const container = document.getElementById('contactsContainer');
        const countElement = document.getElementById('contactCount');

        try {
            container.innerHTML = '<div class="loading">Loading contacts...</div>';

            const response = await fetch(this.apiBase);
            const result = await response.json();

            if (result.success) {
                this.displayContacts(result.data);
                countElement.textContent = result.data.length;
            } else {
                this.showError('Failed to load contacts: ' + result.error);
            }
        } catch (error) {
            this.showError('Network error: ' + error.message);
        }
    }

    displayContacts(contacts) {
        const container = document.getElementById('contactsContainer');

        if (contacts.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No contacts found</h3>
                    <p>Add your first contact using the form on the left.</p>
                </div>
            `;
            return;
        }

        const contactsHtml = contacts.map(contact => this.createContactHtml(contact)).join('');
        container.innerHTML = contactsHtml;

        // Bind delete buttons
        container.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                this.handleDeleteContact(id);
            });
        });
    }

    createContactHtml(contact) {
        const date = new Date(contact.created_at).toLocaleDateString();
        return `
            <div class="contact-item" data-id="${contact.id}">
                <div class="contact-info">
                    <div class="contact-name">${this.escapeHtml(contact.name)}</div>
                    <div class="contact-email">${this.escapeHtml(contact.email_address)}</div>
                    <div class="contact-date">Added: ${date}</div>
                </div>
                <div class="contact-actions">
                    <button class="btn btn-danger delete-btn" data-id="${contact.id}">
                        Delete
                    </button>
                </div>
            </div>
        `;
    }

    async handleAddContact(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const messageElement = document.getElementById('formMessage');

        const contactData = {
            name: formData.get('name').trim(),
            email_address: formData.get('email').trim()
        };

        // Basic validation
        if (!contactData.name || !contactData.email_address) {
            this.showFormMessage('Please fill in all fields.', 'error');
            return;
        }

        if (!this.isValidEmail(contactData.email_address)) {
            this.showFormMessage('Please enter a valid email address.', 'error');
            return;
        }

        try {
            const response = await fetch(this.apiBase, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(contactData)
            });

            const result = await response.json();

            if (result.success) {
                this.showFormMessage('Contact added successfully!', 'success');
                form.reset();
                this.loadContacts(); // Refresh the list
            } else {
                let errorMessage = result.error;
                if (result.errors && result.errors.length > 0) {
                    errorMessage = result.errors.join(', ');
                }
                this.showFormMessage(errorMessage, 'error');
            }
        } catch (error) {
            this.showFormMessage('Network error: ' + error.message, 'error');
        }
    }

    async handleDeleteContact(id) {
        if (!confirm('Are you sure you want to delete this contact?')) {
            return;
        }

        try {
            const response = await fetch(`${this.apiBase}/${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                this.loadContacts(); // Refresh the list
                this.showFormMessage('Contact deleted successfully!', 'success');
            } else {
                this.showFormMessage('Failed to delete contact: ' + result.error, 'error');
            }
        } catch (error) {
            this.showFormMessage('Network error: ' + error.message, 'error');
        }
    }

    showFormMessage(message, type) {
        const messageElement = document.getElementById('formMessage');
        messageElement.textContent = message;
        messageElement.className = `message ${type}`;

        // Auto-hide success messages after 3 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageElement.textContent = '';
                messageElement.className = 'message';
            }, 3000);
        }
    }

    showError(message) {
        const container = document.getElementById('contactsContainer');
        container.innerHTML = `
            <div class="message error">
                ${this.escapeHtml(message)}
            </div>
        `;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize the app when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MailingListApp();
}); 