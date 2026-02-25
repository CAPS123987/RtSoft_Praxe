/// <reference types="cypress" />

describe('Smazání komentáře', () => {

  it('admin může smazat komentář', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

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
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    // Komentáře by neměly mít tlačítko "Smazat" pro nepřihlášeného
    cy.get('.comments').then(($comments) => {
      if ($comments.find('.comment-content').length > 0) {
        cy.get('.comments .comment-content').first().within(() => {
          cy.contains('a', 'Smazat').should('not.exist');
        });
      }
    });
  });

});
