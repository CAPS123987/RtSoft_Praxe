/// <reference types="cypress" />

/**
 * Testy dynamických oprávnění.
 *
 * Postup:
 * 1. Admin vytvoří novou roli s konkrétními oprávněními
 * 2. Admin vytvoří nového uživatele s touto rolí
 * 3. Přihlásí se jako nový uživatel a ověří, že oprávnění fungují
 * 4. Ověří, že zakázané akce nejdou provést
 * 5. Uklidí po sobě – smaže vytvořené uživatele (a s nimi i relace)
 */
describe('Dynamická oprávnění – vytvoření role a uživatele', () => {

  // Sdílená data pro celý describe blok
  const timestamp = Date.now();

  describe('Role s oprávněním addPost + editOwnPost + deleteOwnPost', () => {
    const roleName = `PostRole_${timestamp}`;
    const userName = `postuser_${timestamp}`;
    const userPass = 'test123';
    let createdPostId = null;

    before(() => {
      // 1. Admin vytvoří roli s oprávněními pro posty
      cy.login('admin');
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // 2. Najdeme roli v seznamu a editujeme oprávnění
      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();
      cy.url().should('include', '/admin/edit-role');

      // Nejprve odškrtneme všechna oprávnění
      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });

      // Zaškrtneme požadovaná oprávnění
      cy.get('input[type="checkbox"][name*="addPost"]').check({ force: true });
      cy.get('input[type="checkbox"][name*="editOwnPost"]').check({ force: true });
      cy.get('input[type="checkbox"][name*="deleteOwnPost"]').check({ force: true });
      cy.get('input[type="checkbox"][name*="addComment"]').check({ force: true });

      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // 3. Vytvoříme uživatele s touto rolí
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      // Vybereme roli podle názvu
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');
    });

    it('nový uživatel se může přihlásit', () => {
      cy.loginWith(userName, userPass);
      cy.contains('a', 'Odhlásit').should('be.visible');
    });

    it('nový uživatel vidí tlačítko "Vytvořit příspěvek"', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
    });

    it('nový uživatel může vytvořit příspěvek', () => {
      cy.loginWith(userName, userPass);
      const title = `DynPost_${timestamp}`;
      cy.createTestPost(title, 'Post vytvořený dynamickým uživatelem.');
      cy.get('@createdPostId').then((id) => {
        createdPostId = id;
      });
    });

    it('nový uživatel vidí editaci svého příspěvku', () => {
      cy.loginWith(userName, userPass);
      if (createdPostId) {
        cy.visit(`/post/show/${createdPostId}`);
        cy.contains('a', 'Upravit příspěvek').should('be.visible');
      }
    });

    it('nový uživatel může upravit svůj příspěvek', () => {
      cy.loginWith(userName, userPass);
      if (createdPostId) {
        cy.visit(`/post/show/${createdPostId}`);
        cy.contains('a', 'Upravit příspěvek').click();
        const newContent = `Dynamicky upravený obsah ${Date.now()}`;
        cy.get('textarea[name*="content"]').clear().type(newContent);
        cy.get('input[type="submit"]').click();
        cy.expectToast('úspěšně');
        cy.contains(newContent).should('exist');
      }
    });

    it('nový uživatel vidí smazání svého příspěvku', () => {
      cy.loginWith(userName, userPass);
      if (createdPostId) {
        cy.visit(`/post/show/${createdPostId}`);
        cy.contains('a', 'Smazat příspěvek').should('be.visible');
      }
    });

    it('nový uživatel nemá přístup do admin panelu', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/admin/');
      cy.url().should('include', '/sign/in');
    });

    it('nový uživatel nevidí odkaz Admin v navigaci', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/');
      cy.contains('a', 'Admin').should('not.exist');
    });

    after(() => {
      // Úklid – admin smaže uživatele (kaskádově smaže posty, komentáře, liky)
      cy.login('admin');
      cy.deleteTestUser(userName);
    });

  });

  describe('Role bez oprávnění addPost – uživatel nemůže přidávat příspěvky', () => {
    const roleName = `NoPostRole_${timestamp}`;
    const userName = `nopostuser_${timestamp}`;
    const userPass = 'test123';

    before(() => {
      cy.login('admin');

      // Vytvoříme roli pouze s addComment
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Editujeme oprávnění – pouze addComment
      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();

      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      cy.get('input[type="checkbox"][name*="addComment"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');
    });

    it('uživatel bez addPost nevidí tlačítko "Vytvořit příspěvek"', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('not.exist');
    });

    it('uživatel bez addPost je přesměrován z /edit/create', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/edit/create');
      // Formulář se zobrazí, ale po odeslání dostane chybu oprávnění
      // Nebo nevidí formulář vůbec – záleží na implementaci
      // Minimálně by měl dostat flash message o chybějícím oprávnění
      cy.get('input[name*="title"]').type('Nepovolený post');
      cy.get('textarea[name*="content"]').type('Tento post by neměl projít.');
      cy.get('input[type="submit"]').click();

      // Po odeslání by měl být přesměrován s error flash
      cy.url().should('not.include', '/post/show');
    });

    it('uživatel bez addPost může zobrazit detail postu', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.url().should('include', '/post/show');
    });

    it('uživatel bez addPost nevidí editaci/smazání u cizích postů', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.contains('a', 'Upravit příspěvek').should('not.exist');
      cy.contains('a', 'Smazat příspěvek').should('not.exist');
    });

    after(() => {
      cy.login('admin');
      cy.deleteTestUser(userName);
    });

  });

  describe('Role s oprávněním addComment – uživatel může komentovat', () => {
    const roleName = `CommentRole_${timestamp}`;
    const userName = `commentuser_${timestamp}`;
    const userPass = 'test123';
    let testPostId = null;

    before(() => {
      cy.login('admin');

      // Vytvoříme roli s addComment
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();

      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      cy.get('input[type="checkbox"][name*="addComment"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Admin vytvoří testovací post pro komentování
      cy.createTestPost(`CommentTarget_${timestamp}`, 'Post pro testování komentování.');
      cy.get('@createdPostId').then((id) => {
        testPostId = id;
      });
    });

    after(() => {
      // Úklid – smazat post a uživatele
      cy.login('admin');
      if (testPostId) {
        cy.deleteTestPost(testPostId);
      }
      cy.deleteTestUser(userName);
    });

    it('uživatel s addComment vidí formulář komentáře', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${testPostId}`);
      cy.get('input[name*="email"]').should('be.visible');
      cy.get('input[name*="content"]').should('be.visible');
    });

    it('uživatel s addComment může přidat komentář', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${testPostId}`);

      cy.get('input[name*="email"]').type('dyncomment@test.io');
      cy.get('input[name*="content"]').type(`Dynamický komentář ${Date.now()}`);
      cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();

      cy.expectToast('komentář');
    });

  });

  describe('Role s editAllPost – uživatel může editovat cizí příspěvky', () => {
    const roleName = `EditAllRole_${timestamp}`;
    const userName = `editalluser_${timestamp}`;
    const userPass = 'test123';
    let adminPostId = null;

    before(() => {
      cy.login('admin');

      // Vytvoříme roli s editAllPost
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();

      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      cy.get('input[type="checkbox"][name*="editAllPost"]').check({ force: true });
      cy.get('input[type="checkbox"][name*="addPost"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Admin vytvoří příspěvek
      cy.createTestPost(`EditAllTarget_${timestamp}`, 'Tento post bude editován jiným uživatelem.');
      cy.get('@createdPostId').then((id) => {
        adminPostId = id;
      });
    });

    after(() => {
      cy.login('admin');
      if (adminPostId) {
        cy.deleteTestPost(adminPostId);
      }
      cy.deleteTestUser(userName);
    });

    it('uživatel s editAllPost vidí tlačítko "Upravit příspěvek" u cizího postu', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Upravit příspěvek').should('be.visible');
    });

    it('uživatel s editAllPost může upravit cizí příspěvek', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Upravit příspěvek').click();

      const newContent = `Upraveno uživatelem s editAllPost ${Date.now()}`;
      cy.get('textarea[name*="content"]').clear().type(newContent);
      cy.get('input[type="submit"]').click();
      cy.expectToast('úspěšně');
      cy.contains(newContent).should('exist');
    });

    it('uživatel s editAllPost ale BEZ deleteAllPost nevidí tlačítko smazat u cizího postu', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Smazat příspěvek').should('not.exist');
    });

  });

  describe('Role s deleteAllPost – uživatel může smazat cizí příspěvky', () => {
    const roleName = `DelAllRole_${timestamp}`;
    const userName = `delalluser_${timestamp}`;
    const userPass = 'test123';
    let adminPostId = null;

    before(() => {
      cy.login('admin');

      // Vytvoříme roli s deleteAllPost
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();

      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      cy.get('input[type="checkbox"][name*="deleteAllPost"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Admin vytvoří příspěvek ke smazání
      cy.createTestPost(`DeleteTarget_${timestamp}`, 'Tento post bude smazán jiným uživatelem.');
      cy.get('@createdPostId').then((id) => {
        adminPostId = id;
      });
    });

    it('uživatel s deleteAllPost vidí tlačítko "Smazat příspěvek" u cizího postu', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Smazat příspěvek').should('be.visible');
    });

    it('uživatel s deleteAllPost může smazat cizí příspěvek', () => {
      cy.loginWith(userName, userPass);
      cy.visit(`/post/show/${adminPostId}`);
      cy.contains('a', 'Smazat příspěvek').click();
      cy.expectToast('smazán');
      cy.url().should('not.include', '/post/show');

      // Zrušíme ID, aby after hook neskončil chybou
      adminPostId = null;
    });

    after(() => {
      cy.login('admin');
      if (adminPostId) {
        cy.deleteTestPost(adminPostId);
      }
      cy.deleteTestUser(userName);
    });

  });

});

