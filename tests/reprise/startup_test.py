"""Run with python3 tests/reprise/startup_test.py. No database or Docker required."""
import os
from pathlib import Path
import subprocess
import tempfile
import unittest


class StartupTest(unittest.TestCase):
    def run_startup(self, keys=True, cache_fails=False):
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            log = root / 'commands'
            binaries = root / 'bin'
            binaries.mkdir()
            for name in ('chown', 'chmod', 'php', 'apache2-foreground', 'composer'):
                script = binaries / name
                exit_code = 1 if name == 'php' and cache_fails else 0
                script.write_text('#!/bin/sh\nprintf "%s\\n" "' + name + ' $*" >> "$STARTUP_TEST_LOG"\nexit ' + str(exit_code) + '\n')
                script.chmod(0o755)
            private = root / 'private.pem'
            public = root / 'public.pem'
            if keys:
                private.write_text('persistent-private')
                public.write_text('persistent-public')
            source = (Path(__file__).resolve().parents[2] / 'launchApp.sh').read_text()
            # Only substitute the container working directory, leaving commands intact.
            source = source.replace('cd /var/www', 'cd "$STARTUP_TEST_ROOT"')
            script = root / 'startup.sh'
            script.write_text(source)
            env = dict(os.environ, PATH=str(binaries) + ':' + os.environ['PATH'],
                       STARTUP_TEST_LOG=str(log), STARTUP_TEST_ROOT=str(root),
                       JWT_SECRET_KEY=str(private), JWT_PUBLIC_KEY=str(public),
                       JWT_PASSPHRASE='must-not-be-printed', APP_ENV='prod')
            results = [subprocess.run(['sh', str(script)], env=env, capture_output=True, text=True)]
            if keys and not cache_fails:
                results.append(subprocess.run(['sh', str(script)], env=env, capture_output=True, text=True))
            commands = log.read_text() if log.exists() else ''
            return results, commands, private.read_text() if private.exists() else None

    def test_restarts_preserve_keys_and_do_not_touch_database(self):
        results, commands, key = self.run_startup()
        self.assertTrue(all(result.returncode == 0 for result in results))
        self.assertEqual(key, 'persistent-private')
        self.assertEqual(commands.count('apache2-foreground'), 2)
        for forbidden in ('doctrine:', 'generate-keypair', 'composer', 'must-not-be-printed'):
            self.assertNotIn(forbidden, commands)
            self.assertNotIn(forbidden, ''.join(result.stdout + result.stderr for result in results))

    def test_missing_keys_prevent_startup(self):
        results, commands, _ = self.run_startup(keys=False)
        self.assertNotEqual(results[0].returncode, 0)
        self.assertNotIn('apache2-foreground', commands)

    def test_cache_error_prevents_startup(self):
        results, commands, _ = self.run_startup(cache_fails=True)
        self.assertNotEqual(results[0].returncode, 0)
        self.assertNotIn('apache2-foreground', commands)


if __name__ == '__main__':
    unittest.main()
