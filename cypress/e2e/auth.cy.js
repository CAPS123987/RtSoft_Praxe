/// <reference types="cypress" />

describe('Autentizace', () => {

  it('úspěšné přihlášení', () => {
    cy.login('admin');
    cy.contains('a', 'Odhlásit').should('be.visible');
    cy.url().should('not.include', '/sign/in');
  });

  it('neúspěšné přihlášení – špatné heslo', () => {
    cy.visit('/sign/in');
    cy.get('input[name*="username"]').type('admin');
    cy.get('input[name*="password"]').type('spatneheslo');
    cy.get('input[type="submit"]').click();
    // Formulář by měl zobrazit chybu
    cy.get('.errors').should('contain.text', 'Nesprávné přihlašovací jméno nebo heslo');
  });

  it('neúspěšné přihlášení – prázdné pole', () => {
    cy.visit('/sign/in');
    cy.get('input[type="submit"]').click();
    // Zůstaneme na přihlašovací stránce
    cy.url().should('include', '/sign/in');
  });

  it('odhlášení', () => {
    cy.login('admin');
    cy.logout();
    // Po odhlášení by se měl zobrazit odkaz "Přihlásit"
    cy.contains('a', 'Přihlásit').should('be.visible');
  });

  it('úspěšné přihlášení jako běžný uživatel', () => {
    cy.login('user');
    cy.contains('a', 'Odhlásit').should('be.visible');
  });

  it('po přihlášení je viditelný popover s identitou', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('#profile-hint').should('exist');
    cy.get('#profile-hint').should('contain.text', 'Jméno:');
  });

  it('po odhlášení se nelze vrátit zpět na chráněnou stránku', () => {
    cy.login('admin');
    cy.visit('/edit/create');
    cy.url().should('include', '/edit/create');
    cy.logout();
    cy.visit('/edit/create');
    cy.url().should('satisfy', (url) => {
      return url.includes('/sign/in') || url.includes('/homepage');
    });
  });

});
