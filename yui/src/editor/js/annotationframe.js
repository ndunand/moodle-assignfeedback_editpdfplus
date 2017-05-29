/* global M, Y */

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationframe
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONFRAME = function (config) {
    ANNOTATIONFRAME.superclass.constructor.apply(this, [config]);
};

ANNOTATIONFRAME.NAME = "annotationframe";
ANNOTATIONFRAME.ATTRS = {};

Y.extend(ANNOTATIONFRAME, M.assignfeedback_editpdfplus.annotation, {
    children: [],
    oldx: 0,
    oldy: 0,
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                bounds,
                highlightcolour;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);
        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
            new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        highlightcolour = this.get_color();

        this.shape_id = 'ct_frame_' + (new Date().toJSON()).replace(/:/g, '').replace(/\./g, '');
        shape = this.editor.graphic.addShape({
            id: this.shape_id,
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: {
                weight: 2,
                color: this.get_color()
            },
            x: bounds.x,
            y: bounds.y
        });
        if (this.parent_annot_element) {
            shape.addClass('class_' + this.parent_annot_element.divcartridge);
        }
        if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else if (this.borderstyle === 'dotted') {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }

        drawable.shapes.push(shape);
        this.drawable = drawable;

        this.draw_catridge();

        return ANNOTATIONFRAME.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                highlightcolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = this.get_color();

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 16,
            stroke: {
                weight: 2,
                color: this.get_color()
            },
            x: bounds.x,
            y: edit.start.y - 8
        });
        if (this.parent_annot_element) {
            shape.addClass('class_' + this.parent_annot_element.divcartridge);
        }
        if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else if (this.borderstyle === 'dotted') {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }

        drawable.shapes.push(shape);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y - 8;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 16 - 8;
        this.page = '';

        return (bounds.has_min_width());
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());
    },
    /**
     * Get the color of the element, depend of data on DB
     * @return {string} color
     */
    get_color: function () {
        return this.colour;
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        if (this.parent_annot_element === null && this.parent_annot === 0) {
            var divdisplay;
            var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
            if (this.divcartridge === '') {
                this.init_div_cartridge_id();
                var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

                //rattachement de la shape
                var shapechd = this.editor.graphic.getShapeById(this.shape_id);
                if (shapechd) {
                    shapechd.addClass('class_' + this.divcartridge);
                }

                //init cartridge
                var colorcartridge = this.get_color();
                divdisplay = this.get_div_cartridge(colorcartridge);
                divdisplay.addClass('assignfeedback_editpdfplus_frame');
                divdisplay.setStyles({'border-style': this.borderstyle});
                //divdisplay.set('draggable', 'true');

                // inscription entete
                var divcartridge = this.get_div_cartridge_label(colorcartridge, true);
                divdisplay.append(divcartridge);

                //creation input
                var divconteneurdisplay = this.get_div_container(colorcartridge);
                if (!this.editor.get('readonly')) {
                    var buttonrender = "<button id='" + this.divcartridge + "_buttonpencil' class='btn btn-default' type='button'>";
                    //buttonrender += M.util.image_url('e/text_highlight_picker', 'core');
                    buttonrender += '<i class="fa fa-eyedropper" aria-hidden="true"></i>';
                    buttonrender += "</button>";
                    var buttonrenderdisplay = Y.Node.create(buttonrender);
                    buttonrenderdisplay.on('click', this.display_picker, this);
                    var buttonadd = "<button id='" + this.divcartridge + "_buttonadd' class='btn btn-default' type='button'>";
                    //buttonadd += M.util.image_url('t/add', 'core');
                    buttonadd += '<i class="fa fa-plus" aria-hidden="true"></i>';
                    buttonadd += "</button>";
                    var buttonadddisplay = Y.Node.create(buttonadd);
                    buttonadddisplay.on('click', this.add_annot, this);
                    divconteneurdisplay.append(buttonrenderdisplay);
                    divconteneurdisplay.append(buttonadddisplay);
                }
                divdisplay.append(divconteneurdisplay);

                //creation de la div d'edition
                if (!this.editor.get('readonly')) {
                    var diveditiondisplay = this.get_div_edition();
                    //diveditiondisplay.addClass('assignfeedback_editpdfplus_frame_edition');
                    divconteneurdisplay.append(diveditiondisplay);
                } else {
                    var divvisudisplay = this.get_div_visu(colorcartridge);
                    divconteneurdisplay.append(divvisudisplay);
                }

                //creation de la div palette
                if (!this.editor.get('readonly')) {
                    var styleEditionHtml = "margin:5px;border:2px #ccc ";
                    var styleEditionMinHtml = "min-width:20px;min-height:20px;";

                    var diveditionrender = "<div ";
                    diveditionrender += "id='" + this.divcartridge + "_picker' ";
                    diveditionrender += "class='assignfeedback_editpdfplus_frame_picker' ";
                    diveditionrender += "style='display:none;text-align:right;'> ";
                    diveditionrender += "</div>";
                    var diveditionrenderdisplay = Y.Node.create(diveditionrender);
                    divconteneurdisplay.append(diveditionrenderdisplay);
                    var diveditioncolordisplay = Y.Node.create("<div style='display:inline-block;vertical-align:top;'></div>");
                    var diveditionframedisplay = Y.Node.create("<div style='display:inline-block;vertical-align:top;'></div>");
                    diveditionrenderdisplay.append(diveditioncolordisplay);
                    diveditionrenderdisplay.append(diveditionframedisplay);
                    var diveditionwhitedisplay = Y.Node.create("<div style='background-color:white;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionwhitedisplay.on('click', this.change_color, this, 'white');
                    var diveditionyellowdisplay = Y.Node.create("<div style='background-color:#E69F00;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionyellowdisplay.on('click', this.change_color, this, '#E69F00');//orange
                    var diveditionreddisplay = Y.Node.create("<div style='background-color:#D55E00;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionreddisplay.on('click', this.change_color, this, '#D55E00');//red
                    var diveditiongreendisplay = Y.Node.create("<div style='background-color:#009E73;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditiongreendisplay.on('click', this.change_color, this, '#009E73');//green
                    var diveditionbluedisplay = Y.Node.create("<div style='background-color:#0072B2;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionbluedisplay.on('click', this.change_color, this, '#0072B2');//blue
                    var diveditionblackdisplay = Y.Node.create("<div style='background-color:black;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionblackdisplay.on('click', this.change_color, this, 'black');
                    diveditioncolordisplay.append(diveditionwhitedisplay);
                    diveditioncolordisplay.append(diveditionyellowdisplay);
                    diveditioncolordisplay.append(diveditionreddisplay);
                    diveditioncolordisplay.append(diveditiongreendisplay);
                    diveditioncolordisplay.append(diveditionbluedisplay);
                    diveditioncolordisplay.append(diveditionblackdisplay);
                    var diveditsoliddisplay = Y.Node.create("<div style='"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditsoliddisplay.on('click', this.change_border, this, 'solid');
                    var diveditdotteddisplay = Y.Node.create("<div style='"
                            + styleEditionHtml
                            + "dotted;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditdotteddisplay.on('click', this.change_border, this, 'dotted');
                    var diveditdasheddisplayhtml = "<div style='"
                            + styleEditionHtml
                            + "dashed;"
                            + styleEditionMinHtml + "'>"
                            + "</div>";
                    var diveditiondasheddisplay = Y.Node.create(diveditdasheddisplayhtml);
                    diveditiondasheddisplay.on('click', this.change_border, this, 'dashed');
                    diveditionframedisplay.append(diveditsoliddisplay);
                    diveditionframedisplay.append(diveditdotteddisplay);
                    diveditionframedisplay.append(diveditiondasheddisplay);
                }

                //positionnement de la div par rapport a l'annotation
                if (!this.cartridgex || this.cartridgex === 0) {
                    this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
                }
                if (!this.cartridgey || this.cartridgey === 0) {
                    this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
                }
                divdisplay.setX(this.cartridgex);
                divdisplay.setY(this.y + this.cartridgey);
                drawingregion.append(divdisplay);

                this.apply_visibility_annot();
                if (!this.editor.get('readonly')) {
                    var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
                    var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
                    buttonplusr.hide();
                    buttonplusl.hide();
                }

            } else {
                var divid = '#' + this.divcartridge;
                divdisplay = this.editor.get_dialogue_element(divid);
                divdisplay.setX(offsetcanvas[0] + this.cartridgex);
                divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
            }
        }
        return true;
    },
    /**
     * drag-and-drop process
     * @param {type} e
     */
    move_cartridge_continue: function (e) {
        e.preventDefault();

        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                        e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();

        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;

        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.cartridgex + diffx);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey + diffy);
    },
    /**
     * drag-and-drop stop
     * @param {type} e
     */
    move_cartridge_stop: function (e) {
        e.preventDefault();
        /*var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge + "_cartridge");
         divcartridge.detach('mousemove', this.move_cartridge_continue, this);
         divcartridge.detach('mouseup', this.move_cartridge_stop, this);*/
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        canvas.detach('mousemove', this.move_cartridge_continue, this);
        canvas.detach('mouseup', this.move_cartridge_stop, this);

        //var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
        var clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();

        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;

        this.cartridgex += diffx;
        this.cartridgey += diffy;

        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.cartridgex);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey);

        this.editor.save_current_page();
    },
    /**
     * Add child annotation (new associed frame)
     * @param {type} e
     */
    add_annot: function (e) {
        this.editor.currentedit.parent_annot_element = this;
        this.editor.handle_tool_button(e, TOOLTYPELIB.FRAME, 'ctbutton' + this.toolid, 1);
    },
    /**
     * Display color/border picker toolbar
     */
    display_picker: function () {
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        var buttonrenderdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        divcartridge.setStyle('z-index', 1000);
        divpalette.show();
        buttonrenderdisplay.on('click', this.hide_picker, this);
    },
    /**
     * Hide color/border picker toolbar
     */
    hide_picker: function () {
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        var buttonrenderdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divpalette.hide();
        divcartridge.setStyle('z-index', 0);
        buttonrenderdisplay.on('click', this.display_picker, this);
    },
    /**
     * Apply "change color" on element and children
     * @param {type} e
     * @param {string} colour
     */
    change_color: function (e, colour) {
        this.colour = colour;
        var shape = this.editor.graphic.getShapeById(this.shape_id);
        shape.set("stroke", {
            color: this.colour
        });
        var shapesChildren = null;
        if (this.id) {
            shapesChildren = this.editor.annotationsparent[this.id];
        } else {
            shapesChildren = this.editor.annotationsparent[this.divcartridge];
        }
        if (shapesChildren) {
            for (var i = 0; i < shapesChildren.length; i++) {
                shapesChildren[i].colour = colour;
                var shapechd = this.editor.graphic.getShapeById(shapesChildren[i].shape_id);
                if (shapechd) {
                    shapechd.set("stroke", {
                        color: this.colour
                    });
                }
            }
        }
        var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
        divprincipale.setStyles({
            'border-color': this.colour,
            'color': this.colour
        });
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge + "_cartridge");
        divcartridge.setStyles({
            'border-color': this.colour,
            'color': this.colour
        });
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        divdisplay.setStyles({
            'color': this.colour
        });
        this.hide_picker();
        this.editor.save_current_page();
    },
    /**
     * Apply "change border" on element and children
     * @param {type} e
     * @param {string} colour
     */
    change_border: function (e, border) {
        this.borderstyle = border;
        var shape = this.editor.graphic.getShapeById(this.shape_id);
        if (this.borderstyle === 'solid') {
            shape.set("stroke", {
                dashstyle: 'none'
            });
        } else if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }
        var shapesChildren = [];
        if (this.id) {
            shapesChildren = this.editor.annotationsparent[this.id];
        } else {
            shapesChildren = this.editor.annotationsparent[this.divcartridge];
        }
        if (shapesChildren) {
            for (var i = 0; i < shapesChildren.length; i++) {
                shapesChildren[i].borderstyle = border;
                var shapechd = this.editor.graphic.getShapeById(shapesChildren[i].shape_id);
                if (shapechd) {
                    if (this.borderstyle === 'solid') {
                        shapechd.set("stroke", {
                            dashstyle: 'none'
                        });
                    } else if (this.borderstyle === 'dashed') {
                        shapechd.set("stroke", {
                            dashstyle: [5, 3]
                        });
                    } else {
                        shapechd.set("stroke", {
                            dashstyle: [2, 2]
                        });
                    }
                }
            }
        }
        var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
        divprincipale.setStyles({
            'border-style': this.borderstyle
        });
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        divpalette.hide();
        this.editor.save_current_page();
    },
    /**
     * display annotation edditing view
     */
    edit_annot: function () {
        if (!this.parent_annot_element) {
            var buttonrender = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
            var buttonadd = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonadd");
            this.hide_picker();
            if (buttonrender) {
                buttonrender.hide();
                buttonadd.hide();
            }
            ANNOTATIONFRAME.superclass.edit_annot.call(this);
        }
    },
    /**
     * remove annotation detail view
     * @param {type} e
     * @param {string} clickType
     */
    hide_edit: function () {
        ANNOTATIONFRAME.superclass.hide_edit.call(this);
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        var buttonrender = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        var buttonadd = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonadd");
        var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
        var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
        if (divdisplay) {
            divdisplay.set('style', 'display:inline;color:' + this.get_color() + ';');
            if (buttonrender) {
                buttonrender.show();
                buttonadd.show();
            }
            if (buttonplusr) {
                buttonplusr.hide();
            }
            if (buttonplusl) {
                buttonplusl.hide();
            }
        }
    },
    /**
     * Delete an annotation and its children
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (var k = 0; k < annotations.length; k++) {
            if (annotations[k] === this) {
                if (this.divcartridge !== '') {
                    var divid = '#' + this.divcartridge;
                    var divdisplay = this.editor.get_dialogue_element(divid);
                    divdisplay.remove();
                }
                annotations.splice(k, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }

                var shapesChildren = [];
                if (this.id) {
                    shapesChildren = this.editor.annotationsparent[this.id];
                } else {
                    shapesChildren = this.editor.annotationsparent[this.divcartridge];
                }
                if (shapesChildren) {
                    for (var i = 0; i < shapesChildren.length; i++) {
                        for (var j = 0; j < annotations.length; j++) {
                            if (annotations[j] === shapesChildren[i]) {
                                annotations.splice(j, 1);
                                if (shapesChildren[i].drawable) {
                                    shapesChildren[i].drawable.erase();
                                }
                            }
                        }
                    }
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});
M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationframe = ANNOTATIONFRAME;
