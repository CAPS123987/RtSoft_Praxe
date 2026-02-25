/// <reference types="cypress" />

describe('Navigace', () => {

  it('hlavní navigace obsahuje odkaz na články', () => {
    cy.visit('/');
    cy.contains('a', 'Články').should('be.visible');
  });

  it('klik na "Články" vede na homepage', () => {
    cy.visit('/sign/in');
    cy.contains('a', 'Články').click();
    cy.contains('h1', 'Posty:').should('be.visible');
  });

  it('nepřihlášený uživatel vidí odkaz Přihlásit', () => {
    cy.visit('/');
    cy.contains('a', 'Přihlásit').should('be.visible');
  });

  it('přihlášený uživatel vidí odkaz Odhlásit', () => {
    cy.login('admin');
    cy.contains('a', 'Odhlásit').should('be.visible');
    cy.contains('a', 'Přihlásit').should('not.exist');
  });

  it('klik na Přihlásit vede na přihlašovací stránku', () => {
    cy.visit('/');
    cy.contains('a', 'Přihlásit').click();
    cy.url().should('include', '/sign/in');
    cy.get('input[name*="username"]').should('be.visible');
  });

  it('stránka má správný title', () => {
    cy.visit('/');
    cy.title().should('contain', 'Test project');
  });

  it('admin panel obsahuje navigační odkazy', () => {
    cy.login('admin');
    cy.visit('/admin/');
    cy.contains('a', 'výpis').should('exist');
  });

  it('ze seznamu uživatelů vede odkaz Zpět na admin', () => {
    cy.login('admin');
    cy.visit('/admin/user-list');
    cy.contains('a', 'Zpět').click();
    cy.url().should('include', '/admin');
  });

  it('ze seznamu rolí vede odkaz Zpět na admin', () => {
    cy.login('admin');
    cy.visit('/admin/role-list');
    cy.contains('a', 'Zpět').click();
    cy.url().should('include', '/admin');
  });

  it('z detailu postu lze přejít zpět na seznam postů', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.url().should('include', '/post/show');
    cy.contains('a', 'Články').click();
    cy.contains('h1', 'Posty:').should('be.visible');
  });

  it('přihlašovací stránka má správný nadpis', () => {
    cy.visit('/sign/in');
    cy.contains('h1', 'Přihlášení').should('be.visible');
  });

});
