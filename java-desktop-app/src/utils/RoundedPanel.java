package utils;

import javax.swing.*;
import java.awt.*;
import java.awt.geom.RoundRectangle2D;

/**
 * JPanel avec des coins arrondis
 */
public class RoundedPanel extends JPanel {
    private int cornerRadius = 15;
    private Color backgroundColor;
    private boolean drawShadow = false;

    public RoundedPanel() {
        this(15);
    }

    public RoundedPanel(int radius) {
        super();
        this.cornerRadius = radius;
        setOpaque(false);
    }

    public RoundedPanel(LayoutManager layout, int radius) {
        super(layout);
        this.cornerRadius = radius;
        setOpaque(false);
    }

    public void setBackgroundColor(Color color) {
        this.backgroundColor = color;
        repaint();
    }

    public void setDrawShadow(boolean drawShadow) {
        this.drawShadow = drawShadow;
        repaint();
    }

    @Override
    protected void paintComponent(Graphics g) {
        super.paintComponent(g);
        Graphics2D g2 = (Graphics2D) g.create();
        g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);

        int width = getWidth();
        int height = getHeight();

        // Ombre
        if (drawShadow) {
            g2.setColor(new Color(0, 0, 0, 30));
            g2.fillRoundRect(3, 3, width - 3, height - 3, cornerRadius, cornerRadius);
        }

        // Fond
        Color bgColor = backgroundColor != null ? backgroundColor : getBackground();
        g2.setColor(bgColor);
        g2.fillRoundRect(0, 0, width - (drawShadow ? 3 : 0), height - (drawShadow ? 3 : 0), cornerRadius, cornerRadius);

        g2.dispose();
    }
}
