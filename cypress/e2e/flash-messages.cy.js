/// <reference types="cypress" />

describe('Flash zprávy (toast notifikace)', () => {

  it('po úspěšném odhlášení se zobrazí toast zpráva', () => {
    cy.login('admin');
    cy.logout();
    cy.expectToast('Odhlášení');
  });

  it('po vytvoření postu se zobrazí úspěšný toast', () => {
    cy.login('admin');
    const title = `Toast test ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Testovací obsah.');
    cy.get('input[type="submit"]').click();

    cy.expectToast('úspěšně');
  });

  it('toast zmizí po určité době', () => {
    cy.login('admin');
    cy.logout();

    // Toast by se měl zobrazit
    cy.get('[data-sonner-toast]', { timeout: 8000 }).should('exist');

    // Po čekání by měl zmizet (duration je 2500ms v layout, ale nastavíme na 7000ms na admin stránkách)
    cy.wait(10000);
    cy.get('[data-sonner-toast]').should('not.exist');
  });

});
