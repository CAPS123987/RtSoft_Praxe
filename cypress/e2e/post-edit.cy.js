/// <reference types="cypress" />

describe('Editace postu', () => {
  let testPostId = null;
  const testPostTitle = `EditTest_${Date.now()}`;

  before(() => {
    // Vytvoříme testovací post pro editaci
    cy.login('admin');
    cy.createTestPost(testPostTitle, 'Obsah pro testování editace.');
    cy.get('@createdPostId').then((id) => {
      testPostId = id;
    });
  });

  beforeEach(() => {
    cy.login('admin');
  });

  after(() => {
    // Úklid – smažeme testovací post
    if (testPostId) {
      cy.login('admin');
      cy.deleteTestPost(testPostId);
    }
  });

  it('navigace na editaci přes detail postu', () => {
    cy.visit('/');
    cy.contains('.post h2 a', testPostTitle).click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.url().should('include', '/edit/edit');
  });

  it('formulář je předvyplněný daty postu', () => {
    cy.visit('/');
    cy.contains('.post h2 a', testPostTitle).click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.get('input[name*="title"]').should('not.have.value', '');
    cy.get('textarea[name*="content"]').should('not.have.value', '');
  });

  it('úspěšná editace postu', () => {
    cy.visit('/');
    cy.contains('.post h2 a', testPostTitle).click();
    cy.contains('a', 'Upravit příspěvek').click();

    const suffix = ` (upraveno ${Date.now()})`;
    cy.get('input[name*="title"]').clear().type(testPostTitle + suffix);
    cy.get('input[type="submit"]').click();

    cy.expectToast('úspěšně');
  });

  it('editace postu – formulář obsahuje upload obrázku', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.get('input[name*="postImage"]').should('exist');
  });

});
