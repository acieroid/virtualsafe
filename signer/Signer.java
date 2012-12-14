import java.util.Arrays;

public class Signer {
    public static void main(String[] args) {
        UI ui;
        /* Create the key manager, which read the keys from ~/.signer */
        KeyManager manager = new KeyManager(System.getProperty("user.home") + "/.signer");

        /* Parse arguments */
        if (args.length == 0) {
            /* No arguments, launch the graphical interface */
            /* TODO: implement the graphical interface */
            System.out.println("GUI not implemented, sorry.");
            return;
        } else if (Arrays.asList(args).contains("--help") ||
                   Arrays.asList(args).contains("-h")) {
            usage();
            return;
        } else {
            /* Launch the command line interface */
            CLI cli = new CLI(args);
            if (!cli.valid()) {
                usage();
                return;
            }
            ui = cli;
        }

        if (!manager.keysExists()) {
            ui.askKeys(manager);
        }
        ui.run(manager);
    }

    /**
     * Print the usage of this program
     */
    private static void usage() {
        System.out.println("Usage: signer OPTIONS...");
        System.out.println("Available options:");
        System.out.println("\t[-h|--help]\n\t\tDisplay this help");
        System.out.println("\t[-s file signature]\n\t\tSign 'file', storing the signature in 'signature'");
        System.out.println("\t[-c file signature [certificate]]\n\t\tCheck the signature of a file, with a given certificate (or the user's certificate if none specified");
        System.out.println("\t[-e file_in file_out keyfile]\n\t\tEncrypt a file, storing the result in 'file_out' and the key used, encrypted with the user's public key in 'keyfile'");
        System.out.println("\t[-d file_in file_out keyfile]\n\t\tDecrypt a file, storing the result in 'file_out', using the encrypted secret key from 'key_file'");
        System.out.println("\t[-n]\n\t\tAsk the user for a new certificate");
        System.out.println("\t[--share key_in key_out]\n\t\tDecrypt the key in 'key_in', re-encrypt it with another user public-key (asked interactively) and saves it to 'key_out'");
    }
}
