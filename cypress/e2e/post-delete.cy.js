/// <reference types="cypress" />

describe('Smazání postu', () => {

  it('admin může smazat post', () => {
    // Nejprve vytvoříme nový post, abychom nemazali existující data
    cy.login('admin');

    const title = `Post ke smazání ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Tento post bude smazán.');
    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

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
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Post chráněný proti smazání.');
    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Zjistíme ID postu z URL
    cy.url().then((url) => {
      const postId = url.split('/').pop();

      // Odhlásíme se
      cy.logout();

      // Zkusíme smazat post jako nepřihlášený uživatel
      cy.visit(`/edit/delete-post/${postId}`);

      // Měl by být přesměrován na přihlášení
      cy.url().should('satisfy', (u) => {
        return u.includes('/sign/in') || u.includes('/homepage');
      });
    });
  });

  it('po smazání postu se post nezobrazuje na hlavní stránce', () => {
    cy.login('admin');
    const title = `Post gone ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Tento post zmizí.');
    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Smažeme post
    cy.contains('a', 'Smazat příspěvek').click();
    cy.expectToast('smazán');

    // Ověříme, že se post nezobrazuje na hlavní stránce
    cy.visit('/');
    cy.contains('.post h2 a', title).should('not.exist');
  });

});
