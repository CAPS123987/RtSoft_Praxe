/// <reference types="cypress" />

describe('Nahrávání obrázku k postu', () => {
  const createdPostIds = [];

  beforeEach(() => {
    cy.login('admin');
  });

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

  it('formulář pro vytvoření postu obsahuje input pro obrázek', () => {
    cy.visit('/edit/create');
    cy.get('input[name*="postImage"]').should('exist');
  });

  it('úspěšné vytvoření postu s obrázkem', () => {
    const title = `Post s obrázkem ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Post s testovacím obrázkem.');

    cy.get('input[name*="postImage"]').selectFile({
      contents: Cypress.Buffer.from(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        'base64'
      ),
      fileName: 'test-image.png',
      mimeType: 'image/png',
    });

    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Uložíme ID pro úklid
    cy.url().then((url) => {
      const postId = url.split('/').pop();
      cy.wrap(postId).as('createdPostId');
    });
  });

  it('po vytvoření postu s obrázkem je obrázek viditelný na detailu', () => {
    const title = `Post img detail ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Post s obrázkem pro kontrolu.');

    cy.get('input[name*="postImage"]').selectFile({
      contents: Cypress.Buffer.from(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        'base64'
      ),
      fileName: 'test-detail.png',
      mimeType: 'image/png',
    });

    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Na detailu postu by měl být obrázek
    cy.get('.post img').should('be.visible');

    // Uložíme ID pro úklid
    cy.url().then((url) => {
      const postId = url.split('/').pop();
      cy.wrap(postId).as('createdPostId');
    });
  });

  it('vytvoření postu bez obrázku – obrázek se nezobrazí', () => {
    const title = `Post bez obrázku ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Post bez obrázku.');
    cy.get('input[type="submit"]').click();

    cy.expectToast('úspěšně');
    // Na detailu by neměl být žádný obrázek postu
    cy.get('.post img').should('not.exist');

    // Uložíme ID pro úklid
    cy.url().then((url) => {
      const postId = url.split('/').pop();
      cy.wrap(postId).as('createdPostId');
    });
  });

  it('editace postu – nahrání nového obrázku přepíše starý', () => {
    const title = `Post img replace ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Post pro výměnu obrázku.');
    cy.get('input[name*="postImage"]').selectFile({
      contents: Cypress.Buffer.from(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        'base64'
      ),
      fileName: 'original.png',
      mimeType: 'image/png',
    });
    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Uložíme ID pro úklid
    cy.url().then((url) => {
      const postId = url.split('/').pop();
      cy.wrap(postId).as('createdPostId');
    });

    // Editujeme a nahrajeme nový obrázek
    cy.contains('a', 'Upravit příspěvek').click();
    cy.get('input[name*="postImage"]').selectFile({
      contents: Cypress.Buffer.from(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAweABl0F+PcAAAAASUVORK5CYII=',
        'base64'
      ),
      fileName: 'replaced.png',
      mimeType: 'image/png',
    });
    cy.get('input[type="submit"]').click();
    cy.expectToast('úspěšně');

    // Obrázek by měl stále existovat
    cy.get('.post img').should('be.visible');
  });

});
