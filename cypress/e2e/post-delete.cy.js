/// <reference types="cypress" />

describe('Smazání postu', () => {

  it('admin může smazat post', () => {
    // Vytvoříme nový post, abychom nemazali existující data
    cy.login('admin');

    const title = `Post ke smazání ${Date.now()}`;
    cy.createTestPost(title, 'Tento post bude smazán.');

    // Nyní najdeme post a smažeme ho
    cy.visit('/');
    cy.contains('.post h2 a', title).click();
    cy.contains('a', 'Smazat příspěvek').click();

    // Po smazání by mělo přesměrovat na homepage s flash zprávou
    cy.expectToast('smazán');
    cy.url().should('not.include', '/post/show');
  });

  it('nepřihlášený uživatel nemůže smazat post přes URL', () => {
    // Nejprve jako admin vytvoříme post a zjistíme jeho URL
    cy.login('admin');
    const title = `Post protect ${Date.now()}`;
    cy.createTestPost(title, 'Post chráněný proti smazání.');

    // Zjistíme ID postu z URL
    cy.get('@createdPostId').then((postId) => {
      // Odhlásíme se
      cy.logout();

      // Zkusíme smazat post jako nepřihlášený uživatel
      cy.visit(`/edit/delete-post/${postId}`);

      // Měl by být přesměrován na přihlášení
      cy.url().should('satisfy', (u) => {
        return u.includes('/sign/in') || u.includes('/homepage');
      });

      // Úklid – přihlásíme se a smažeme testovací post
      cy.login('admin');
      cy.deleteTestPost(postId);
    });
  });

  it('po smazání postu se post nezobrazuje na hlavní stránce', () => {
    cy.login('admin');
    const title = `Post gone ${Date.now()}`;
    cy.createTestPost(title, 'Tento post zmizí.');

    // Smažeme post
    cy.contains('a', 'Smazat příspěvek').click();
    cy.expectToast('smazán');

    // Ověříme, že se post nezobrazuje na hlavní stránce
    cy.visit('/');
    cy.contains('.post h2 a', title).should('not.exist');
  });

});
