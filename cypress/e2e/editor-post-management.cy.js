/// <reference types="cypress" />

describe('Editor – správa vlastních příspěvků', () => {
  let editorPostId = null;
  const editorPostTitle = `EditorPost_${Date.now()}`;

  before(() => {
    // Editor vytvoří svůj příspěvek
    cy.login('editor');
    cy.createTestPost(editorPostTitle, 'Obsah příspěvku vytvořeného editorem.');
    cy.get('@createdPostId').then((id) => {
      editorPostId = id;
    });
  });

  after(() => {
    // Úklid – smažeme testovací post (pokud ještě existuje)
    if (editorPostId) {
      cy.login('editor');
      cy.visit(`/post/show/${editorPostId}`, { failOnStatusCode: false });
      cy.url().then((url) => {
        if (url.includes(`/post/show/${editorPostId}`)) {
          cy.get('body').then(($body) => {
            if ($body.find('a:contains("Smazat příspěvek")').length > 0) {
              cy.contains('a', 'Smazat příspěvek').click();
            }
          });
        }
      });
    }
  });

  describe('Vytvoření příspěvku', () => {

    it('editor vidí tlačítko "Vytvořit příspěvek" na hlavní stránce', () => {
      cy.login('editor');
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
    });

    it('editor může přistoupit na stránku pro vytvoření příspěvku', () => {
      cy.login('editor');
      cy.visit('/edit/create');
      cy.url().should('include', '/edit/create');
      cy.get('input[name*="title"]').should('be.visible');
      cy.get('textarea[name*="content"]').should('be.visible');
    });

    it('editor úspěšně vytvoří příspěvek', () => {
      cy.login('editor');
      const title = `EditorCreate_${Date.now()}`;
      cy.createTestPost(title, 'Editor vytvořil tento příspěvek.');

      // Úklid – ihned smažeme
      cy.contains('a', 'Smazat příspěvek').click();
      cy.expectToast('smazán');
    });

  });

  describe('Úprava vlastního příspěvku', () => {

    it('editor vidí tlačítko "Upravit příspěvek" u svého postu', () => {
      cy.login('editor');
      cy.visit(`/post/show/${editorPostId}`);
      cy.contains('a', 'Upravit příspěvek').should('be.visible');
    });

    it('editor může přejít na editaci svého postu', () => {
      cy.login('editor');
      cy.visit(`/post/show/${editorPostId}`);
      cy.contains('a', 'Upravit příspěvek').click();
      cy.url().should('include', '/edit/edit');
    });

    it('editor úspěšně upraví svůj příspěvek', () => {
      cy.login('editor');
      cy.visit(`/post/show/${editorPostId}`);
      cy.contains('a', 'Upravit příspěvek').click();

      const newContent = `Upravený obsah editorem ${Date.now()}`;
      cy.get('textarea[name*="content"]').clear().type(newContent);
      cy.get('input[type="submit"]').click();

      cy.expectToast('úspěšně');
      cy.contains(newContent).should('exist');
    });

  });

  describe('Smazání vlastního příspěvku', () => {

    it('editor vidí tlačítko "Smazat příspěvek" u svého postu', () => {
      cy.login('editor');
      cy.visit(`/post/show/${editorPostId}`);
      cy.contains('a', 'Smazat příspěvek').should('be.visible');
    });

    it('editor úspěšně smaže svůj příspěvek', () => {
      cy.login('editor');

      // Vytvoříme post ke smazání
      const title = `EditorDelete_${Date.now()}`;
      cy.createTestPost(title, 'Tento post editor smaže.');

      cy.contains('a', 'Smazat příspěvek').click();
      cy.expectToast('smazán');
      cy.url().should('not.include', '/post/show');

      // Ověříme, že post zmizel z hlavní stránky
      cy.visit('/');
      cy.contains('.post h2 a', title).should('not.exist');
    });

  });

  describe('Editor nemůže upravit/smazat cizí příspěvek', () => {

    let adminPostId = null;
    const adminPostTitle = `AdminPost_${Date.now()}`;

    before(() => {
      // Admin vytvoří příspěvek
      cy.login('admin');
      cy.createTestPost(adminPostTitle, 'Příspěvek vytvořený adminem.');
      cy.get('@createdPostId').then((id) => {
        adminPostId = id;
      });
    });

    after(() => {
      // Úklid – admin smaže svůj post
      if (adminPostId) {
        cy.login('admin');
        cy.deleteTestPost(adminPostId);
      }
    });

    it('editor nevidí tlačítko "Upravit příspěvek" u cizího postu', () => {
      cy.login('editor');
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Upravit příspěvek').should('not.exist');
    });

    it('editor nevidí tlačítko "Smazat příspěvek" u cizího postu', () => {
      cy.login('editor');
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Smazat příspěvek').should('not.exist');
    });

    it('editor je přesměrován při pokusu o editaci cizího postu přes URL', () => {
      cy.login('editor');
      cy.visit(`/edit/edit/${adminPostId}`);
      // Měl by být přesměrován s chybovou hláškou
      cy.url().should('not.include', `/edit/edit/${adminPostId}`);
    });

    it('editor je přesměrován při pokusu o smazání cizího postu přes URL', () => {
      cy.login('editor');
      cy.visit(`/edit/delete-post/${adminPostId}`);
      // Měl by být přesměrován s chybovou hláškou
      cy.url().should('not.include', `/edit/delete-post/${adminPostId}`);
    });

  });

  describe('Editor a admin panel', () => {

    it('editor nemá přístup do admin panelu', () => {
      cy.login('editor');
      cy.visit('/admin/');
      cy.url().should('include', '/sign/in');
    });

    it('editor nevidí odkaz Admin v navigaci', () => {
      cy.login('editor');
      cy.visit('/');
      cy.contains('a', 'Admin').should('not.exist');
    });

  });

});

