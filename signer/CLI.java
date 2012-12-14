public class CLI implements UI {
    /** Mode to sign a file */
    private static final int SIGN = 1;
    /** Mode to check a signature */
    private static final int CHECK = 2;
    /** Mode to decrypt a file */
    private static final int DECRYPT = 3;
    /** Invalid mode */
    private static final int INVALID = 4;

    /**
     * The chosen mode
     */
    private int mode;

    /**
     * The possible parameters
     */
    private String fileIn = null, fileOut = null, signature = null, keyFile = null;

    public CLI(String[] args) {
        /* Parse parameters */
        if (args.length < 1) {
            mode = INVALID;
        } if ((args[0].equals("-s") || args[0].equals("--sign")) &&
              args.length == 3) {
            /* Signature */
            mode = SIGN;
            fileIn = args[1];
            signature = args[2];
        } else if ((args[0].equals("-c") || args[0].equals("--check")) &&
                   args.length == 3) {
            /* Two-argument check */
            mode = CHECK;
            fileIn = args[1];
            signature = args[2];
        } else if ((args[0].equals("-c") || args[0].equals("--check")) &&
                   args.length == 4) {
            /* Three-argument check */
            mode = CHECK;
            fileIn = args[1];
            signature = args[2];
            keyFile = args[3];
        } else if ((args[0].equals("-d") || args[0].equals("--decrypt")) &&
                   args.length == 4) {
            /* Decryption */
            mode = DECRYPT;
            fileIn = args[1];
            fileOut = args[2];
            keyFile = args[3];
        } else {
            mode = INVALID;
        }
    }

    /**
     * Return true if the arguments passed were valid
     */
    public boolean valid() {
        return mode != INVALID;
    }

    public void askKeys(KeyManager manager) {
        System.out.println("Please paste your certificate and private key:");
        if (manager.parse(System.in)) {
            System.out.println("Thank you, the key is now saved");
        } else {
            System.out.println("ERROR: cannot parse the key, please check if you correctly copied it");
        }
    }

    public void run(KeyManager manager) {
        switch (mode) {
        case SIGN:
            if (manager.sign(fileIn, signature)) {
                System.out.println("File signed with success, signature saved to " + signature);
            } else {
                System.out.println("ERROR: cannot sign the file");
            }
            break;
        case CHECK:
            if (keyFile == null && manager.check(fileIn, signature)) {
                /* Two-arguments check */
                System.out.println("The signature matches your file");
            } else if (keyFile != null && manager.check(fileIn, signature, keyFile)) {
                /* Three-arguments check */
                System.out.println("The signature matches the user's file");
            } else {
                System.out.println("ERROR: the signature did not match the file. This file could be compromised. Please contact the sender of this file.");
            }
            break;
        case DECRYPT:
            if (manager.decrypt(fileIn, fileOut, keyFile)) {
                System.out.println("The file " + fileIn + " has been decrypted with success to the file " + fileOut);
            } else {
                System.out.println("ERROR: the file could not be decrypted");
            }
            break;
        default:
            System.out.println("ERROR: No mode specified.");
        }
    }
}
