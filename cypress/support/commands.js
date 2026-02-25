// ***********************************************
// Custom Cypress Commands
// ***********************************************

/**
 * Přihlášení přes UI formulář.
 * Přijímá klíč z fixtures/users.json (např. 'admin', 'user').
 * Credentials se načítají automaticky z fixture souboru.
 */
Cypress.Commands.add('login', (role = 'admin') => {
  cy.fixture('users').then((users) => {
    const user = users[role];
    if (!user) {
      throw new Error(`Role "${role}" nebyla nalezena v users.json`);
    }
    cy.visit('/sign/in');
    cy.get('input[name*="username"]').clear().type(user.username);
    cy.get('input[name*="password"]').clear().type(user.password);
    cy.get('input[type="submit"]').click();
    // Po úspěšném přihlášení by měl být vidět odkaz "Odhlásit"
    cy.contains('a', 'Odhlásit').should('be.visible');
  });
});

/**
 * Odhlášení.
 */
Cypress.Commands.add('logout', () => {
  cy.contains('a', 'Odhlásit').click();
});

/**
 * Kontrola toast zprávy (vanilla-sonner).
 * Toast se zobrazí jako <li> uvnitř #sonner-toast-container.
 */
Cypress.Commands.add('expectToast', (message) => {
  cy.get('[data-sonner-toast]', { timeout: 8000 })
    .should('contain.text', message);
});

