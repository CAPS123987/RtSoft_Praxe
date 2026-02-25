/// <reference types="cypress" />

describe('Zobrazení postu', () => {

  it('zobrazí detail postu s titulkem, datem a obsahem', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().invoke('text').as('postTitle');
    cy.get('.post h2 a').first().click();

    cy.get('@postTitle').then((title) => {
      cy.get('h2').should('contain.text', title.trim());
    });
    cy.get('.date').should('exist');
    cy.get('.post').should('exist');
  });

  it('zobrazí sekci komentářů', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('h2', 'Komentáře:').should('be.visible');
  });

  it('zobrazí like tlačítko', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('#like-btn').should('be.visible');
    cy.get('.like-count').should('exist');
  });

  it('přihlášený uživatel vidí tlačítka upravit/smazat u svého postu', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    // Admin by měl vidět alespoň jedno z tlačítek
    cy.get('body').then(($body) => {
      const hasEdit = $body.find('a:contains("Upravit příspěvek")').length > 0;
      const hasDelete = $body.find('a:contains("Smazat příspěvek")').length > 0;
      expect(hasEdit || hasDelete).to.be.true;
    });
  });

  it('nepřihlášený uživatel nevidí tlačítka pro správu postů', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').should('not.exist');
    cy.contains('a', 'Smazat příspěvek').should('not.exist');
  });

  it('detail postu zobrazuje obsah postu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('.post').should('exist');
    cy.get('.post').should('not.be.empty');
  });

  it('like tlačítko nepřihlášeného uživatele má data-logged-in=false', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'false');
  });

  it('like tlačítko přihlášeného uživatele má data-logged-in=true', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'true');
  });

  it('komentáře mají správnou strukturu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('.comment-content').then(($comments) => {
      if ($comments.length > 0) {
        cy.get('.comment-content').first().within(() => {
          cy.get('h4').should('exist');
        });
      }
    });
  });

});
