/// <reference types="cypress" />

describe('Flash zprávy (toast notifikace)', () => {
  const createdPostIds = [];

  afterEach(function () {
    if (this.createdPostId) {
      createdPostIds.push(this.createdPostId);
    }
  });

  after(() => {
    // Úklid – smažeme všechny vytvořené posty
    if (createdPostIds.length > 0) {
      cy.login('admin');
      createdPostIds.forEach((id) => {
        cy.deleteTestPost(id);
      });
    }
  });

  it('po úspěšném odhlášení se zobrazí toast zpráva', () => {
    cy.login('admin');
    cy.logout();
    cy.expectToast('Odhlášení');
  });

  it('po vytvoření postu se zobrazí úspěšný toast', () => {
    cy.login('admin');
    const title = `Toast test ${Date.now()}`;
    cy.createTestPost(title);
  });

  it('toast zmizí po určité době', () => {
    cy.login('admin');
    cy.logout();

    // Toast by se měl zobrazit
    cy.get('[data-sonner-toast]', { timeout: 10000 }).should('exist');

    // Po čekání by měl zmizet (duration je 2500ms v layout, ale nastavíme na 7000ms na admin stránkách)
    cy.wait(10000);
    cy.get('[data-sonner-toast]').should('not.exist');
  });

});
