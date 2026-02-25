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
  cy.get('[data-sonner-toast]', { timeout: 10000 })
    .should('contain.text', message);
});

/**
 * Vytvoří testovací post a vrátí jeho ID (přes alias @createdPostId).
 * Předpokládá, že uživatel je přihlášen.
 */
Cypress.Commands.add('createTestPost', (title, content = 'Testovací obsah vytvořený Cypress testem.') => {
  cy.visit('/edit/create');
  cy.get('input[name*="title"]').type(title);
  cy.get('textarea[name*="content"]').type(content);
  cy.get('input[type="submit"]').click();
  cy.expectToast('úspěšně');

  // Uložíme URL, ze které extrahujeme ID postu
  cy.url().then((url) => {
    const postId = url.split('/').pop();
    cy.wrap(postId).as('createdPostId');
  });
});

/**
 * Smaže post podle ID přes UI (navigace na detail a klik na smazat).
 * Předpokládá, že uživatel je přihlášen jako admin.
 */
Cypress.Commands.add('deleteTestPost', (postId) => {
  cy.visit(`/edit/delete-post/${postId}`);
  // Může přesměrovat na homepage po smazání
  cy.url().should('not.include', `/edit/delete-post/${postId}`);
});

/**
 * Přihlášení s přímým zadáním credentials (pro dynamicky vytvořené uživatele).
 */
Cypress.Commands.add('loginWith', (username, password) => {
  cy.visit('/sign/in');
  cy.get('input[name*="username"]').clear().type(username);
  cy.get('input[name*="password"]').clear().type(password);
  cy.get('input[type="submit"]').click();
  cy.contains('a', 'Odhlásit').should('be.visible');
});

/**
 * Smaže testovací post podle titulku přes hlavní stránku.
 * Předpokládá, že uživatel je přihlášen jako admin.
 */
Cypress.Commands.add('deleteTestPostByTitle', (title) => {
  cy.visit('/');
  cy.get('body').then(($body) => {
    if ($body.find(`.post h2 a:contains("${title}")`).length > 0) {
      cy.contains('.post h2 a', title).click();
      cy.contains('a', 'Smazat příspěvek').click();
    }
  });
});

/**
 * Smaže testovacího uživatele podle jména přes admin panel.
 * Předpokládá, že uživatel je přihlášen jako admin s oprávněním deleteUser.
 * Kaskádově smaže i všechny relace (posty, komentáře, liky).
 */
Cypress.Commands.add('deleteTestUser', (username) => {
  cy.visit('/admin/user-list');
  cy.get('body').then(($body) => {
    if ($body.text().includes(username)) {
      cy.contains('[class*="border"]', username)
        .contains('a', 'Smazat').click();
      cy.expectToast('smazán');
    }
  });
});

