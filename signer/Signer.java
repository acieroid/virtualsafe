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
        System.out.println("Usage: signer [-h|--help] [-s file signature] [-c file signature [certificate]] [-d file_in file_out keyfile] [-n new_certificate_file]");
    }
}
