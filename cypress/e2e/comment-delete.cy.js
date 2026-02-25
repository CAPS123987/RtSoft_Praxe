/// <reference types="cypress" />

describe('Smazání komentáře', () => {
  let testPostId = null;
  const testPostTitle = `CommentDelTest_${Date.now()}`;

  before(() => {
    // Vytvoříme vlastní testovací post
    cy.login('admin');
    cy.createTestPost(testPostTitle, 'Post pro testování mazání komentářů.');
    cy.get('@createdPostId').then((id) => {
      testPostId = id;
    });
  });

  after(() => {
    // Úklid – smažeme testovací post (smaže i komentáře)
    if (testPostId) {
      cy.login('admin');
      cy.deleteTestPost(testPostId);
    }
  });

  it('admin může smazat komentář', () => {
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);

    // Nejdříve přidáme komentář, abychom měli co smazat
    cy.get('input[name*="email"]').type('delete-test@cypress.io');
    cy.get('input[name*="content"]').type('Komentář ke smazání');
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();
    cy.expectToast('komentář');

    // Počkáme a znovu načteme stránku
    cy.reload();

    // Najdeme komentář a smažeme ho
    cy.get('.comments .comment-content').should('have.length.greaterThan', 0);
    cy.get('.comments .comment-content').last().within(() => {
      cy.contains('a', 'Smazat').click();
    });

    cy.expectToast('smazán');
  });

  it('nepřihlášený uživatel nevidí tlačítko smazat', () => {
    // Nejprve přidáme komentář jako admin
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);
    cy.get('input[name*="email"]').type('nobutton@cypress.io');
    cy.get('input[name*="content"]').type('Komentář pro test viditelnosti tlačítka');
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();
    cy.expectToast('komentář');

    // Odhlásíme se a zkontrolujeme
    cy.logout();
    cy.visit(`/post/show/${testPostId}`);

    cy.get('.comments').then(($comments) => {
      if ($comments.find('.comment-content').length > 0) {
        cy.get('.comments .comment-content').first().within(() => {
          cy.contains('a', 'Smazat').should('not.exist');
        });
      }
    });
  });

});
