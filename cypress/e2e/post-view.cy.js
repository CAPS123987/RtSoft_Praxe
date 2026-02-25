/// <reference types="cypress" />

describe('Zobrazení postu', () => {
  let testPostId = null;
  const testPostTitle = `ViewTest_${Date.now()}`;

  before(() => {
    // Vytvoříme vlastní testovací post
    cy.login('admin');
    cy.createTestPost(testPostTitle, 'Obsah pro testování zobrazení postu.');
    cy.get('@createdPostId').then((id) => {
      testPostId = id;
    });
  });

  after(() => {
    // Úklid – smažeme testovací post
    if (testPostId) {
      cy.login('admin');
      cy.deleteTestPost(testPostId);
    }
  });

  it('zobrazí detail postu s titulkem, datem a obsahem', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.get('h2').should('contain.text', testPostTitle);
    cy.get('.date').should('exist');
    cy.get('.post').should('exist');
  });

  it('zobrazí sekci komentářů', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.contains('h2', 'Komentáře:').should('be.visible');
  });

  it('zobrazí like tlačítko', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.get('#like-btn').should('be.visible');
    cy.get('.like-count').should('exist');
  });

  it('přihlášený uživatel vidí tlačítka upravit/smazat u svého postu', () => {
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);
    cy.get('body').then(($body) => {
      const hasEdit = $body.find('a:contains("Upravit příspěvek")').length > 0;
      const hasDelete = $body.find('a:contains("Smazat příspěvek")').length > 0;
      expect(hasEdit || hasDelete).to.be.true;
    });
  });

  it('nepřihlášený uživatel nevidí tlačítka pro správu postů', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.contains('a', 'Upravit příspěvek').should('not.exist');
    cy.contains('a', 'Smazat příspěvek').should('not.exist');
  });

  it('detail postu zobrazuje obsah postu', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.get('.post').should('exist');
    cy.get('.post').should('not.be.empty');
  });

  it('like tlačítko nepřihlášeného uživatele má data-logged-in=false', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'false');
  });

  it('like tlačítko přihlášeného uživatele má data-logged-in=true', () => {
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'true');
  });

  it('komentáře mají správnou strukturu', () => {
    cy.visit(`/post/show/${testPostId}`);
    cy.get('.comment-content').then(($comments) => {
      if ($comments.length > 0) {
        cy.get('.comment-content').first().within(() => {
          cy.get('h4').should('exist');
        });
      }
    });
  });

});
