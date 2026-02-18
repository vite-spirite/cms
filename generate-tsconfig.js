import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Équivalent de __dirname en ES module
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

function getModulesAliases() {
    const coreModulePath = path.resolve(__dirname, 'app/Core');
    const modulesPath = path.resolve(__dirname, 'app/Modules');
    const aliases = {};

    try {
        // Core modules (prioritaires)
        if (fs.existsSync(coreModulePath)) {
            const coreModules = fs.readdirSync(coreModulePath);

            coreModules.forEach((module) => {
                const modulePath = path.resolve(coreModulePath, module);

                if (fs.statSync(modulePath).isDirectory()) {
                    const resourcePath = path.resolve(modulePath, 'Resources/js');

                    if (fs.existsSync(resourcePath)) {
                        aliases[`@modules/${module}/*`] = [`./app/Core/${module}/Resources/js/*`];
                    }
                }
            });
        }

        // Custom modules (si pas déjà défini dans Core)
        if (fs.existsSync(modulesPath)) {
            const modules = fs.readdirSync(modulesPath);

            modules.forEach((module) => {
                const modulePath = path.resolve(modulesPath, module);

                if (fs.statSync(modulePath).isDirectory() && !aliases[`@modules/${module}/*`]) {
                    const resourcePath = path.resolve(modulePath, 'Resources/js');

                    if (fs.existsSync(resourcePath)) {
                        aliases[`@modules/${module}/*`] = [`./app/Modules/${module}/Resources/js/*`];
                    }
                }
            });
        }
    } catch (error) {
        console.warn('⚠️  Error scanning modules:', error.message);
    }

    return aliases;
}

const moduleAliases = getModulesAliases();

const tsconfigModules = {
    compilerOptions: {
        baseUrl: '.',
        paths: moduleAliases,
    },
    include: ['app/Core/**/*.ts', 'app/Core/**/*.vue', 'app/Modules/**/*.ts', 'app/Modules/**/*.vue'],
};

// Écrire le fichier
fs.writeFileSync(path.resolve(__dirname, 'tsconfig.modules.json'), JSON.stringify(tsconfigModules, null, 2));

console.log('✅ tsconfig.modules.json generated successfully!');
console.log(
    '📦 Found modules:',
    Object.keys(moduleAliases)
        .map((k) => k.replace('@modules/', ''))
        .join(', ') || 'none',
);
