import './commands';

// Ignorovat uncaught exceptions z cross-origin skriptů (CDN: tailwind, bootstrap, sonner atd.)
Cypress.on('uncaught:exception', (err, runnable) => {
  // Vrátíme false, aby Cypress neselhal kvůli chybám z aplikace
  if (err.message.includes('Script error')) {
    return false;
  }
  // Ignorovat i další běžné chyby z externích knihoven
  if (err.message.includes('ResizeObserver') || err.message.includes('Non-Error')) {
    return false;
  }
  // UMD moduly z CDN (vanilla-sonner, invokers apod.) používají `module` objekt, který v prohlížeči neexistuje
  if (err.message.includes('module is not defined')) {
    return false;
  }
  // Ostatní chyby nechat projít
  return true;
});

